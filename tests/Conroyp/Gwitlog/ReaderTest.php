<?php

namespace Conroyp\Gwitlog;

/**
 * Tests for the Gwitlog reader class
 *
 * PHP Version 5.3
 *
 * @category Tests
 * @package  Gwitlog
 * @author   Paul Conroy <paul@conroyp.com>
 * @license  MIT
 * @link     N/A
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    protected $goodCommit;
    protected $goodCommitWithBranch;

    /**
     * Set up common post data for "good" tests
     *
     * @return void
     */
    protected function setUp()
    {
        $this->log = array(
            'hash'      =>  '3a9cfa81b20c88f38787955095ee1cbac4b69abc',
            'branch'    =>  'origin/my-branch-name, my-branch-name',
            'message'   =>  'Testing, good line',
            'date'      =>  'Fri Aug 15 18:54:51 2014 +0100',
            'username'  =>  'Paul Conroy',
            'email'     =>  'paul@conroyp.com',
            'timestamp' =>  '1408125291'
        );

        $this->goodCommit = $this->log['hash'] . ' - '
            . $this->log['message'] . ' (' . $this->log['date'] . ') '
            . '<' . $this->log['username'] . ':' . $this->log['email'] . '>';

        $this->goodCommitWithBranch = $this->log['hash'] . ' - ('
            . $this->log['branch'] . ') '
            . $this->log['message'] . ' (' . $this->log['date'] . ') '
            . '<' . $this->log['username'] . ':' . $this->log['email'] . '>';
    }


    /**
     * Validation of a log line when given in the expected format
     *
     * @return void
     */
    public function testValidateLogFormat()
    {
        $this->assertTrue(Reader::validateLogFormat($this->goodCommit));
    }


    /**
     * Validation of a log line when given in the expected format, and also
     * containing a branch name
     *
     * @return void
     */
    public function testValidateLogFormatWithBranch()
    {
        $this->assertTrue(
            Reader::validateLogFormat($this->goodCommitWithBranch)
        );
    }


    /**
     * Validation behaviour when given an otherwise-valid line with a short-form
     * hash. We need the long form to enable the GH/BB linking
     *
     * @return void
     */
    public function testValidateLogFormatWithShortHash()
    {
        $line = '3a9cfa8 - (origin/my-branch-name, my-branch-name) Testing, bad line '
            . '(Fri Aug 15 18:54:51 2014 +0100) <conroyp:paul@conroyp.com>';
        $this->assertFalse(Reader::validateLogFormat($line));
    }


    /**
     * Validation behaviour when given an otherwise-valid line with a short date format
     *
     * @return void
     */
    public function testValidateLogFormatWithShortDateFormat()
    {
        $line = '3a9cfa81b20c88f38787955095ee1cbac4b69abc - Testing, bad date format '
            . '(Fri Aug 15 18:54:51 2014) <conroyp:paul@conroyp.com>';
        $this->assertFalse(Reader::validateLogFormat($line));
    }


    /**
     * Validation behaviour when given a line that doesn't match the expected format
     *
     * @return void
     */
    public function testValidateLogFormatWithInvalidFormat()
    {
        $line = 'Testing, invalid format (Fri Aug 15 18:54:51 2014) <conroyp:paul@conroyp.com>';
        $this->assertFalse(Reader::validateLogFormat($line));
    }


    /**
     * Validation behaviour when given an empty line
     *
     * @return void
     */
    public function testValidateLogFormatWithEmptyString()
    {
        $this->assertFalse(Reader::validateLogFormat(''));
    }


    /**
     * Test the extraction of a timestamp in to distinct parts
     *
     * @depends testValidateLogFormat
     *
     * @return void
     */
    public function testHydrate()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);
        $this->assertEquals($gwitlog->hash, $this->log['hash']);
        $this->assertEquals($gwitlog->branch, $this->log['branch']);
        $this->assertEquals($gwitlog->message, $this->log['message']);
        $this->assertEquals($gwitlog->date, $this->log['date']);
        $this->assertEquals($gwitlog->timestamp, $this->log['timestamp']);
        $this->assertEquals($gwitlog->username, $this->log['username']);
        $this->assertEquals($gwitlog->email, $this->log['email']);
    }


    /**
     * Prettification of a successful response code
     *
     * @return void
     */
    public function testExtractTimestamp()
    {
        $this->assertEquals(
            $this->log['timestamp'],
            Reader::extractTimestamp($this->goodCommitWithBranch)
        );
    }


    /**
     * Extraction of timestamp when date is found that got past our format
     * checks but is nonsensical (Feb 29 9999 etc)
     *
     * @depends testValidateLogFormat
     *
     * @return void
     */
    public function testExtractTimestampInvalidFormat()
    {
        $line = '3a9cfa81b20c88f38787955095ee1cbac4b69abc - Testing, bad date '
            . '(Boo Foo 29 18:54:51 9999 +0200) <conroyp:paul@conroyp.com>';
        $this->assertFalse(Reader::extractTimestamp($line));
    }


    /**
     * Extract user name from the commit
     *
     * @return void
     */
    public function testExtractUsername()
    {
        $this->assertEquals(
            $this->log['username'],
            Reader::extractUsername($this->goodCommit)
        );
    }


    /**
     * Test that extracting user names with accented characters from the commit
     * works as expected
     *
     * @return void
     */
    public function testExtractUsernameNonEnglishCharacters()
    {
        $line = '3a9cfa81b20c88f38787955095ee1cbac4b69abc - Testing, good line '
            . '(Fri Aug 15 18:54:51 2014 +0100) <Paúl Cánäré:paul@conroyp.com>';

        $this->assertEquals(
            'Paúl Cánäré',
            Reader::extractUsername($line)
        );
    }


    /**
     * Test extracting the hash from the log line
     *
     * @return void
     */
    public function testExtractHash()
    {
        $this->assertEquals(
            $this->log['hash'],
            Reader::extractHash($this->goodCommit)
        );
    }


    /**
     * Test the setting of a provider base to github
     *
     * @return void
     */
    public function testSetRemoteHostGithub()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);
        // Valid link, should be true on assignment
        $this->assertTrue($gwitlog->setRemoteHost('https://github.com/conroyp/gwitlog'));
    }


    /**
     * Test the setting of a provider base to github, but with http as url base
     *
     * @return void
     */
    public function testSetRemoteHostGithubNonHttps()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);
        // Valid link, should be true on assignment
        $this->assertTrue($gwitlog->setRemoteHost('http://github.com/conroyp/gwitlog'));
    }


    /**
     * Test the setting of a provider base to github without both owner and repo set
     *
     * @return void
     */
    public function testSetRemoteHostGithubInvalidRepositoryInformation()
    {
        $this->setExpectedException('\Conroyp\Gwitlog\Exception\InvalidRepositoryInformation');
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);
        // Valid link, should be true on assignment
        $this->assertTrue($gwitlog->setRemoteHost('https://github.com/gwitlog-one-big-long-string//'));
    }


    /**
     * Test the setting of a provider base to bitbucket
     *
     * @return void
     */
    public function testSetRemoteHostBitbucket()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);
        // Valid link, should be true on assignment
        $this->assertTrue($gwitlog->setRemoteHost('https://bitbucket.org/conroyp/gwitlog'));
    }


    /**
     * Test the setting of a remote host with and without a trailing slash
     *
     * @return void
     */
    public function testSetRemoteHostTrailingSlash()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);

        $gwitlog->setRemoteHost('https://bitbucket.org/conroyp/gwitlog/');
        $hasSlash = $gwitlog->getRemoteHost();
        $gwitlog->setRemoteHost('https://bitbucket.org/conroyp/gwitlog');
        $hasNoSlash = $gwitlog->getRemoteHost();
        $this->assertEquals(
            $hasSlash,
            $hasNoSlash
        );
    }


    /**
     * Test the setting of a provider base to bitbucket but with a http base
     *
     * @return void
     */
    public function testSetRemoteHostBitbucketNonHttps()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);
        // Valid link, should be true on assignment
        $this->assertTrue($gwitlog->setRemoteHost('http://bitbucket.org/conroyp/gwitlog'));
    }


    /**
     * Test the setting of a provider base to github without both owner and repo set
     *
     * @return void
     */
    public function testSetRemoteHostBitbucketInvalidRepositoryInformation()
    {
        $this->setExpectedException('\Conroyp\Gwitlog\Exception\InvalidRepositoryInformation');
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);
        // Valid link, should be true on assignment
        $this->assertTrue($gwitlog->setRemoteHost('https://bitbucket.org/gwitlog-one-big-long-string//'));
    }


    /**
     * Test the setting of a provider base to an unknown host
     *
     * @return void
     */
    public function testSetRemoteHostUnknown()
    {
        $this->setExpectedException('\Conroyp\Gwitlog\Exception\InvalidHost');

        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);

        $this->assertTrue($gwitlog->setRemoteHost('https://foobaretc.com/conroyp/gwitlog'));
    }


    /**
     * Test the checking of whether a valid remote host has been set
     *
     * @return void
     */
    public function testHasRemoteHost()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);

        $gwitlog->setRemoteHost('https://bitbucket.org/conroyp/gwitlog/');

        $this->assertTrue($gwitlog->hasRemoteHost());
    }


    /**
     * Test the checking of whether a valid remote host has been set when none have been
     * provided
     *
     * @return void
     */
    public function testHasRemoteHostNoneGiven()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);

        $this->assertFalse($gwitlog->hasRemoteHost());
    }


    /**
     * Test the formatting of a hash link for the github provider
     *
     * @depends testSetRemoteHostGithub
     *
     * @return void
     */
    public function testGetRemoteLinkForGithub()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);
        $gwitlog->setRemoteHost('https://github.com/conroyp/gwitlog');

        $this->assertEquals(
            'https://github.com/conroyp/gwitlog/commit/' . $this->log['hash'],
            $gwitlog->getRemoteLink()
        );
    }


    /**
     * Test the formatting of a hash link for the bitbucket provider
     *
     * @depends testSetRemoteHostBitbucket
     *
     * @return void
     */
    public function testGetRemoteLinkForBitbucket()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);
        $gwitlog->setRemoteHost('https://bitbucket.org/conroyp/gwitlog');

        $this->assertEquals(
            'https://bitbucket.org/conroyp/gwitlog/commits/' . $this->log['hash'],
            $gwitlog->getRemoteLink()
        );
    }


    /**
     * Test the formatting of a hash link when no remote provider exists
     *
     * @depends testSetRemoteHostBitbucket
     *
     * @return void
     */
    public function testGetRemoteLinkWithoutProvider()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);

        // With no provider set, the "link" will be a local one
        $this->assertEquals(
            '#' . $this->log['hash'],
            $gwitlog->getRemoteLink()
        );
    }


    /**
     * Test the retrieval of the date in ISO8601 format
     *
     * @return void
     */
    public function testGetDateIso8601()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);

        // With no provider set, the "link" will be a local one
        $this->assertEquals(
            '2014-08-15T18:54:51+0100',
            $gwitlog->getDateIso8601()
        );
    }


    /**
     * Test the retrieval of the short-form hash, similar to that seen on
     * github and bitbucket
     *
     * @return void
     */
    public function testGetShortHash()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);

        // With no provider set, the "link" will be a local one
        $this->assertEquals(
            '3a9cfa81b2',
            $gwitlog->getShortHash()
        );
    }


    /**
     * Text message extraction
     *
     * @return void
     */
    public function testExtractMessage()
    {
        $this->assertEquals(
            $this->log['message'],
            Reader::extractMessage($this->goodCommit)
        );
    }


    /**
     * Test getting the gravatar url for the identified user
     *
     * @return void
     */
    public function testGetGravatar()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);

        // With no provider set, the "link" will be a local one
        $this->assertEquals(
            'http://www.gravatar.com/avatar/51ceeb04efedef9e425b940e150d49f4.jpg',
            $gwitlog->getGravatar()
        );
    }


    /**
     * Test getting the gravatar url for the identified user, with a dimension
     * of 100px
     *
     * @return void
     */
    public function testGetGravatarCustomSize()
    {
        $gwitlog = new Reader();
        $gwitlog->hydrate($this->goodCommitWithBranch);
        $size = 100;

        // With no provider set, the "link" will be a local one
        $this->assertEquals(
            'http://www.gravatar.com/avatar/51ceeb04efedef9e425b940e150d49f4.jpg?s=100',
            $gwitlog->getGravatar($size)
        );
    }
}
