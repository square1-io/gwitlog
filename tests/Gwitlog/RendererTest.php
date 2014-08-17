<?php

namespace Gwitlog;

/**
 * Tests for the Gwitlog rendering class
 *
 * PHP Version 5.3
 *
 * @category Tests
 * @package  Gwitlog
 * @author   Paul Conroy <paul@conroyp.com>
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
        $this->setExpectedException('\Gwitlog\Exception\MissingInputSource');

        $gwitlog = new \Gwitlog\Renderer();
        $gwitlog->outputToFile('outputfile.txt');
    }


    /**
     * Test the behaviour of outputToFile when no filename is given
     *
     * @return void
     */
    public function testOutputToFileWithNoOutputFile()
    {
        $this->setExpectedException('\Gwitlog\Exception\MissingOutputFile');

        $gwitlog = new \Gwitlog\Renderer();
        $gwitlog->setInputFile(__DIR__ . '/../../phpunit.xml');
        $gwitlog->outputToFile('');
    }


    /**
     * Test the behaviour of render when no source has been previously given
     *
     * @return void
     */
    public function testRenderWithNoInputFile()
    {
        $this->setExpectedException('\Gwitlog\Exception\MissingInputSource');

        $gwitlog = new \Gwitlog\Renderer();
        $gwitlog->render();
    }
}
