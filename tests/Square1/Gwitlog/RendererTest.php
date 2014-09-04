<?php

namespace Square1\Gwitlog;

/**
 * Tests for the Gwitlog rendering class
 *
 * PHP Version 5.3
 *
 * @category Tests
 * @package  Gwitlog
 * @author   Paul Conroy <paul@square1.io>
 * @license  MIT
 * @link     N/A
 */
class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the behaviour of outputToFile when no source has been previously given
     *
     * @return void
     */
    public function testOutputToFileWithNoInputFile()
    {
        $this->setExpectedException('\Square1\Gwitlog\Exception\MissingInputSource');

        $gwitlog = new Renderer();
        $gwitlog->outputToFile('outputfile.txt');
    }


    /**
     * Test the behaviour of outputToFile when no filename is given
     *
     * @return void
     */
    public function testOutputToFileWithNoOutputFile()
    {
        $this->setExpectedException('\Square1\Gwitlog\Exception\MissingOutputFile');

        $gwitlog = new Renderer();
        // This needs to be a real file path or a framework exception is thrown
        $gwitlog->setInputFile(__FILE__);
        $gwitlog->outputToFile('');
    }


    /**
     * Test the behaviour of render when no source has been previously given
     *
     * @return void
     */
    public function testRenderWithNoInputFile()
    {
        $this->setExpectedException('\Square1\Gwitlog\Exception\MissingInputSource');

        $gwitlog = new Renderer();
        $gwitlog->render();
    }
}
