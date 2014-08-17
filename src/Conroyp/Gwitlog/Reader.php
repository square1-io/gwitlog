<?php

namespace Conroyp\Gwitlog;

/**
 * Handle the reading of a git log entry, extracting key info to support the Renderer class
 *
 * PHP Version 5.3
 *
 * @category Reader
 * @package  Gwitlog
 * @author   Paul Conroy <paul@conroyp.com>
 * @license  MIT
 * @link     N/A
 */
class Reader
{
    private static $hashMatch = '^(?<hash>[0-9a-z]{40})';
    private static $branchMatch = '(\((?<branch>.*)\) )?';
    private static $messageMatch = '(?<message>.*)';
    private static $dateMatch = '(\((?<date>[a-z]{3} [a-z]{3} [0-9]{1,2} [0-9]{2}\:[0-9]{2}\:[0-9]{2} [0-9]{4} [+-][0-9]{4})\))';
    private static $usernameMatch = '(?<username>.*)';
    private static $emailMatch = '(?<email>.*)';

    // Remote repo (github/bitbucket) base. Default to hash (local link) until set
    private $remoteHost = '#';

    /**
     * Build the regex to do a full line match
     *
     * @return string Full regex
     */
    private static function buildFullMatchRegex()
    {
        return self::$hashMatch . ' - ' . self::$branchMatch . self::$messageMatch
            . ' ' . self::$dateMatch . ' <' . self::$usernameMatch . ':'
            . self::$emailMatch . '>';
    }


    /**
     * Ensure that the line we've been given is in the right format
     *
     * @param string $line Log line
     *
     * @return bool Determine whether line is valid or not
     */
    public static function validateLogFormat($line)
    {
        $regex = self::buildFullMatchRegex();
        preg_match('/' . $regex . '/i', $line, $matches);

        return count($matches) > 0;
    }


    /**
     * Given a log line, extract it in to member variables
     *
     * @param string $line Git log line
     *
     * @return void
     */
    public function hydrate($line)
    {
        if (!self::validateLogFormat($line)) {
            throw new Exception\InvalidLogFormat('Invalid log format received');
        }

        $regex = self::buildFullMatchRegex();
        preg_match('/' . $regex . '/i', $line, $matches);

        foreach ($matches as $key => $value) {
            if (is_numeric($key)) {
                continue;
            }
            $this->$key = $value;
            if ($key == 'date') {
                $this->timestamp = strtotime($value);
            }
        }
    }


    /**
     * Set the remote host to be linked for each commit
     * Supported currently: bitbucket and github
     *
     * @param string $url Remote url base
     *
     * @return bool True if host was set ok. Throw exception otherwise
     */
    public function setRemoteHost($url)
    {
        // First, check if we've been given a url starting http instead of https
        if (preg_match('/^http:/', $url)) {
            $url = str_replace('http:', 'https:', $url);
        }

        $providers = array(
            'github.com'    =>  'commit',
            'bitbucket.org' =>  'commits'
        );
        // Check that we're coming from a legit host
        if (!preg_match('#https://('. implode('|', array_keys($providers)) . ')/#', $url)) {
            throw new Exception\InvalidHost('Unsupported remote host: ' . $url);
        }

        // Check that we have both repo and owner
        if (!preg_match('#https://(' . implode('|', array_keys($providers)) . ')/([^/]+)/([^/]+)(/)?#', $url)) {
            throw new Exception\InvalidRepositoryInformation(
                "Invalid repository information (owner and repo given)?: " . $url
            );
        }

        // Ensure we always have a trailing slash
        $this->remoteHost = rtrim($url, '/') . '/';

        // Now add the 'commit' separator, prepping the url for hash addition
        foreach ($providers as $base => $commitStub) {
            if (preg_match('#' . $base . '#', $url)) {
                $this->remoteHost .= $commitStub . '/';
                break;
            }
        }

        // All matched ok? No exceptions so far, so we're good
        return true;
    }


    /**
     * Get the remote host value
     *
     * @return string Remote host
     */
    public function getRemoteHost()
    {
        return $this->remoteHost;
    }


    /**
     * Check whether a remote host has been set
     *
     * @return bool Whether a non-default host has been set
     */
    public function hasRemoteHost()
    {
        return $this->remoteHost != "#";
    }


    /**
     * Get the remote host value
     *
     * @return string Remote host
     */
    public function getRemoteLink()
    {
        return $this->remoteHost . $this->hash;
    }


    /**
     * Get the short-form hash. The full hash typically abbreviated when viewing
     * the github/bitbucket web UI, so match this.
     *
     * @return string Substring of the commit hash
     */
    public function getShortHash()
    {
        return substr($this->hash, 0, 10);
    }


    /**
     * Generate the gravatar url for the given user's email
     *
     * @param int $size Optional sizing of image, up to a max of 2048
     * @return string Gravatar url
     */
    public function getGravatar($size = '')
    {
        $gravatar = 'http://www.gravatar.com/avatar/' . md5($this->email) . '.jpg';
        if (!empty($size)) {
            $gravatar .= '?s=' . $size;
        }

        return $gravatar;
    }


    /**
     * Get the tweet date in ISO8601 format
     *
     * @return string ISO8601-formatted date
     */
    public function getDateIso8601()
    {
        return date(\DateTime::ISO8601, $this->timestamp);
    }


    /**
     * Extract the date from the commit and convert it to a timestamp
     *
     * @param string $line Git log line
     *
     * @return timestamp
     */
    public static function extractTimestamp($line)
    {
        preg_match('/' . self::$dateMatch . '/i', $line, $matches);

        return strtotime($matches['date']);
    }


    /**
     * Extract the commit hash from the log line
     *
     * @param string $line Git log line
     *
     * @return timestamp
     */
    public static function extractHash($line)
    {
        preg_match('/' . self::$hashMatch . '/i', $line, $matches);
        return $matches['hash'];
    }


    /**
     * Extract the commit message from the log line
     *
     * @param string $line Git log line
     *
     * @return timestamp
     */
    public static function extractMessage($line)
    {
        // Need the full line regex as the message regex alone is greedy
        $regex = self::buildFullMatchRegex();
        preg_match('/' . $regex . '/i', $line, $matches);

        return $matches['message'];
    }


    /**
     * Extract the username from the log line
     *
     * @param string $line Git log line
     *
     * @return timestamp
     */
    public static function extractUsername($line)
    {
        // Need the full line regex as the username regex alone is greedy
        $regex = self::buildFullMatchRegex();
        preg_match('/' . $regex . '/i', $line, $matches);

        return $matches['username'];
    }


    /**
     * String representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return "Commit: " . $this->hash . "\n"
            .  "Author: " . $this->username . " (" . $this->email . ")\n"
            .  "Date:   " . $this->date . "\n\n"
            .  "\t" . $this->message . "\n\n";
    }
}
