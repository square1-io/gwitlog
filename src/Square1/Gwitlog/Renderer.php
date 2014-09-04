<?php

namespace Square1\Gwitlog;

/**
 * Handle the output rendering of git log entries
 *
 * PHP Version 5.3
 *
 * @category Reader
 * @package  Gwitlog
 * @author   Paul Conroy <paul@square1.io>
 * @license  MIT
 * @link     N/A
 */
class Renderer
{
    private $inputSource;
    private $gwitlog;
    private $repoName;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->gwitlog = new Reader();
    }


    /**
     * Set the repository name
     *
     * @param string $name Repository name
     *
     * @return void
     */
    public function setRepoName($name)
    {
        $this->repoName = $name;
    }


    /**
     * Set the remote host to be linked for each commit
     * Supported currently: bitbucket and github
     *
     * @param string $url Remote url base
     *
     * @return void
     */
    public function setRemoteHost($url)
    {
        $this->gwitlog->setRemoteHost($url);
    }


    /**
     * Set the name of the input file to use
     *
     * @param string $filename Path to file input
     *
     * @return void
     */
    public function setInputFile($filename)
    {
        $stream = fopen($filename, 'r');
        $this->setInputStream($stream);
    }


    /**
     * Set the input stream to use
     *
     * @param string $stream Input stream
     *
     * @return void
     */
    public function setInputStream($stream)
    {
        $this->inputSource = $stream;
    }


    /**
     * Generate the Gwitlog, saved to the given file
     *
     * @param string $filename Locatio to which output should be written
     *
     * @return void
     */
    public function outputToFile($filename)
    {
        if (empty($this->inputSource)) {
            throw new Exception\MissingInputSource('No input source provided');
        }

        if (empty($filename)) {
            throw new Exception\MissingOutputFile('No output file provided');
        }

        // Set up output file
        $fh = fopen($filename, 'w');

        // Header first
        // Was repo name set?
        if (!empty($this->repoName)) {
            $repo = $this->repoName;
        }
        ob_start();
        include $this->getView('header.php');
        $header = ob_get_clean();
        fwrite($fh, $header);

        while ($line = fgets($this->inputSource)) {
            $this->gwitlog->hydrate($line);
            // Echo line item
            $gweet = $this->gwitlog;
            ob_start();
            include $this->getView('gweet.php');
            $gweet = ob_get_clean();
            fwrite($fh, $gweet);
        }

        // Footer
        ob_start();
        include $this->getView('footer.php');
        $footer = ob_get_clean();
        fwrite($fh, $footer);

        // Close input and output streams
        fclose($fh);
        fclose($this->inputSource);
    }


    /**
     * Generate the gwitlog, writing it to the current output stream
     *
     * @return void
     */
    public function render()
    {
        if (empty($this->inputSource)) {
            throw new Exception\MissingInputSource('No input source provided');
        }

        // Was repo name set?
        if (!empty($this->repoName)) {
            $repo = $this->repoName;
        }
        // Render header
        ob_start();
        include $this->getView('header.php');
        echo ob_get_clean();

        while ($line = fgets($this->inputSource)) {
            $this->gwitlog->hydrate($line);
            // Echo line item
            $gweet = $this->gwitlog;
            // Echo line item
            ob_start();
            include $this->getView('gweet.php');
            echo ob_get_clean();
        }

        // Render header
        ob_start();
        include $this->getView('footer.php');
        echo ob_get_clean();

        // Close stream
        fclose($this->inputSource);
    }


    /**
     * Get the path to a given view
     *
     * @param string $file Filename
     *
     * @return Full path
     */
    public function getView($file)
    {
        return __DIR__ . '/../../views/' . $file;
    }
}
