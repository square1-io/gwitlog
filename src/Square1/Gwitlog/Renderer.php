<?php

namespace Square1\Gwitlog;

use Philo\Blade\Blade;

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

    // Default templates to use
    private $templates = array(
        'header'    =>  'header',
        'gwit'      =>  'gwit',
        'footer'    =>  'footer'
    );

    private $viewDirectory;
    private $cacheDirectory;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->gwitlog = new Reader();

        // Default view and cache directories for blade templating
        $this->viewDirectory = __DIR__ . '/../../views';
        // Needs to be outside install path to ensure web user has write permission
        $this->cacheDirectory = '/tmp';

        $this->setViewDirectory($this->viewDirectory);
        $this->setCacheDirectory($this->cacheDirectory);
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

        $header = $this->blade->view()->make($this->templates['header']);
        fwrite($fh, $header);

        while ($line = fgets($this->inputSource)) {
            $this->gwitlog->hydrate($line);
            // Echo line item
            $gwit = $this->blade->view()->make(
                $this->templates['gwit'],
                array('gwit'    =>  $this->gwitlog)
            );
            fwrite($fh, $gwit);
        }

        // Footer
        $header = $this->blade->view()->make($this->templates['footer']);
        fwrite($fh, $header);

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
        echo $this->blade->view()->make($this->templates['header']);

        while ($line = fgets($this->inputSource)) {
            $this->gwitlog->hydrate($line);
            // Echo line item
            $gwit = $this->gwitlog;
            // Echo line item
            echo $this->blade->view()->make(
                $this->templates['gwit'],
                array('gwit'    =>  $gwit)
            );
        }

        // Footer
        echo $this->blade->view()->make($this->templates['footer']);

        // Close stream
        fclose($this->inputSource);
    }


    /**
     * Specify the default view directory
     *
     * @param string $path The directory in which views are to be found
     *
     * @return void
     */
    public function setViewDirectory($path)
    {
        $this->viewDirectory = $path;
        $this->blade = new Blade($this->viewDirectory, $this->cacheDirectory);
    }


    /**
     * Specify the default cache directory
     *
     * @param string $path The directory in which cached views are to be stored
     *
     * @return void
     */
    public function setCacheDirectory($path)
    {
        $this->cacheDirectory = $path;
        $this->blade = new Blade($this->viewDirectory, $this->cacheDirectory);
    }


    /**
     * Specify a custom header template
     *
     * @param string $path The header template name, minus '.blade.php'
     *
     * @return void
     */
    public function setHeaderTemplate($template)
    {
        $this->templates['header'] = $template;
    }


    /**
     * Specify a custom template for the individual commit log messages
     *
     * @param string $path The template name, minus '.blade.php'
     *
     * @return void
     */
    public function setGwitTemplate($template)
    {
        $this->templates['gwit'] = $template;
    }


    /**
     * Specify a custom footer template
     *
     * @param string $path The footer template name, minus '.blade.php'
     *
     * @return void
     */
    public function setFooterTemplate($template)
    {
        $this->templates['footer'] = $template;
    }
}
