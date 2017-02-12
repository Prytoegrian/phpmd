<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2008-2017, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\TextUI;

use PHPMD\AbstractTest;
use PHPMD\Rule;
use PHPMD\Stubs\WriterStub;

/**
 * Test case for the {@link \PHPMD\TextUI\CommandLineOptions} class.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\TextUI\CommandLineOptions
 * @group phpmd
 * @group phpmd::textui
 * @group unittest
 */
class CommandLineOptionsTest extends AbstractTest
{
    /**
     * @var resource
     */
    private $stderrStreamFilter;

    /**
     * @return void
     */
    protected function tearDown()
    {
        if (is_resource($this->stderrStreamFilter)) {
            stream_filter_remove($this->stderrStreamFilter);
        }
        $this->stderrStreamFilter = null;

        parent::tearDown();
    }

    /**
     * testAssignsInputArgumentToInputProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsInputArgumentToInputProperty()
    {
        $args = array('foo.php', __FILE__, 'text', 'design');
        $opts = new CommandLineOptions($args);

        self::assertEquals(__FILE__, $opts->getInputPath());
    }

    /**
     * testAssignsFormatArgumentToReportFormatProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentToReportFormatProperty()
    {
        $args = array('foo.php', __FILE__, 'text', 'design');
        $opts = new CommandLineOptions($args);

        self::assertEquals('text', $opts->getReportFormat());
    }

    /**
     * testAssignsRuleSetsArgumentToRuleSetProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsRuleSetsArgumentToRuleSetProperty()
    {
        $args = array('foo.php', __FILE__, 'text', 'design');
        $opts = new CommandLineOptions($args);

        self::assertEquals('design', $opts->getRuleSets());
    }

    /**
     * testThrowsExpectedExceptionWhenRequiredArgumentsNotSet
     *
     * @return void
     * @since 1.1.0
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExpectedExceptionWhenRequiredArgumentsNotSet()
    {
        $args = array(__FILE__, 'text', 'design');
        new CommandLineOptions($args);
    }

    /**
     * testAssignsInputFileOptionToInputPathProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsInputFileOptionToInputPathProperty()
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = array('foo.php', 'text', 'design', '--inputfile', $uri);
        $opts = new CommandLineOptions($args);

        self::assertEquals('Dir1/Class1.php,Dir2/Class2.php', $opts->getInputPath());
    }

    /**
     * testAssignsFormatArgumentCorrectWhenCalledWithInputFile
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentCorrectWhenCalledWithInputFile()
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = array('foo.php', 'text', 'design', '--inputfile', $uri);
        $opts = new CommandLineOptions($args);

        self::assertEquals('text', $opts->getReportFormat());
    }

    /**
     * testAssignsRuleSetsArgumentCorrectWhenCalledWithInputFile
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsRuleSetsArgumentCorrectWhenCalledWithInputFile()
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = array('foo.php', 'text', 'design', '--inputfile', $uri);
        $opts = new CommandLineOptions($args);

        self::assertEquals('design', $opts->getRuleSets());
    }

    /**
     * testThrowsExpectedExceptionWhenInputFileNotExists
     *
     * @return void
     * @since 1.1.0
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExpectedExceptionWhenInputFileNotExists()
    {
        $args = array('foo.php', 'text', 'design', '--inputfile', 'inputfail.txt');
        new CommandLineOptions($args);
    }

    /**
     * testHasVersionReturnsFalseByDefault
     *
     * @return void
     */
    public function testHasVersionReturnsFalseByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'unusedcode');
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->hasVersion());
    }

    /**
     * testCliOptionsAcceptsVersionArgument
     *
     * @return void
     */
    public function testCliOptionsAcceptsVersionArgument()
    {
        $args = array(__FILE__, '--version');
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->hasVersion());
    }

    /**
     * Tests if ignoreViolationsOnExit returns false by default
     *
     * @return void
     */
    public function testIgnoreViolationsOnExitReturnsFalseByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'unusedcode');
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->ignoreViolationsOnExit());
    }

    /**
     * Tests if CLI options accepts ignoreViolationsOnExit argument
     *
     * @return void
     */
    public function testCliOptionsAcceptsIgnoreViolationsOnExitArgument()
    {
        $args = array(__FILE__, __FILE__, 'text', 'unusedcode', '--ignore-violations-on-exit');
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->ignoreViolationsOnExit());
    }

    /**
     * Tests if CLI usage contains ignoreViolationsOnExit option
     *
     * @return void
     */
    public function testCliUsageContainsIgnoreViolationsOnExitOption()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertContains('--ignore-violations-on-exit:', $opts->usage());
    }

    /**
     * testCliUsageContainsStrictOption
     *
     * @return void
     */
    public function testCliUsageContainsStrictOption()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertContains('--strict:', $opts->usage());
    }

    /**
     * testCliOptionsIsStrictReturnsFalseByDefault
     *
     * @return void
     * @since 1.2.0
     */
    public function testCliOptionsIsStrictReturnsFalseByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->hasStrict());
    }

    /**
     * testCliOptionsAcceptsStrictArgument
     *
     * @return void
     * @since 1.2.0
     */
    public function testCliOptionsAcceptsStrictArgument()
    {
        $args = array(__FILE__, '--strict', __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->hasStrict());
    }

    /**
     * @return void
     */
    public function testCliOptionsAcceptsMinimumpriorityArgument()
    {
        $args = array(__FILE__, '--minimumpriority', 42, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertEquals(42, $opts->getMinimumPriority());
    }

    /**
     * @return void
     */
    public function testGetMinimumPriorityReturnsLowestValueByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertEquals(Rule::LOWEST_PRIORITY, $opts->getMinimumPriority());
    }

    /**
     * @return void
     */
    public function testGetCoverageReportReturnsNullByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertNull($opts->getCoverageReport());
    }

    /**
     * @return void
     */
    public function testGetCoverageReportWithCliOption()
    {
        $opts = new CommandLineOptions(
            array(
                __FILE__,
                __FILE__,
                'text',
                'codesize',
                '--coverage',
                __METHOD__
            )
        );

        $this->assertEquals(__METHOD__, $opts->getCoverageReport());
    }

    /**
     * @param string $reportFormat
     * @param string $expectedClass
     * @return void
     * @dataProvider dataProviderCreateRenderer
     */
    public function testCreateRenderer($reportFormat, $expectedClass)
    {
        $args = array(__FILE__, __FILE__, $reportFormat, 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertInstanceOf($expectedClass, $opts->createRenderer(new WriterStub(), $reportFormat));
    }

    /**
     * @return array
     */
    public function dataProviderCreateRenderer()
    {
        return array(
            array('html', 'PHPMD\\Renderer\\HtmlRenderer'),
            array('text', 'PHPMD\\Renderer\\TextRenderer'),
            array('xml', 'PHPMD\\Renderer\\XmlRenderer'),
            array('PHPMD_Test_Renderer_PEARRenderer', 'PHPMD_Test_Renderer_PEARRenderer'),
            array('PHPMD\\Test\\Renderer\\NamespaceRenderer', 'PHPMD\\Test\\Renderer\\NamespaceRenderer'),
            /* Test what happens when class already exists. */
            array('PHPMD\\Test\\Renderer\\NamespaceRenderer', 'PHPMD\\Test\\Renderer\\NamespaceRenderer'),
        );
    }

    /**
     * @param string $reportFormat
     * @return void
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp (^Can\'t )
     * @dataProvider dataProviderCreateRendererThrowsException
     */
    public function testCreateRendererThrowsException($reportFormat)
    {
        $args = array(__FILE__, __FILE__, $reportFormat, 'codesize');
        $opts = new CommandLineOptions($args);
        $opts->createRenderer();
    }

    /**
     * @return array
     */
    public function dataProviderCreateRendererThrowsException()
    {
        return array(
            array(''),
            array('PHPMD\\Test\\Renderer\\NotExistsRenderer')
        );
    }

    /**
     * @param string $deprecatedName
     * @param string $newName
     * @dataProvider dataProviderDeprecatedCliOptions
     */
    public function testDeprecatedCliOptions($deprecatedName, $newName)
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM works different here.');
        }

        stream_filter_register('stderr_stream', 'PHPMD\\TextUI\\StreamFilter');

        $this->stderrStreamFilter = stream_filter_prepend(STDERR, 'stderr_stream');

        $args = array(__FILE__, __FILE__, 'text', 'codesize', sprintf('--%s', $deprecatedName), 42);
        new CommandLineOptions($args);

        $this->assertContains(
            sprintf(
                'The --%s option is deprecated, please use --%s instead.',
                $deprecatedName,
                $newName
            ),
            StreamFilter::$streamHandle
        );
    }

    /**
     * @return array
     */
    public function dataProviderDeprecatedCliOptions()
    {
        return array(
            array('extensions', 'suffixes'),
            array('ignore', 'exclude')
        );
    }

    /**
     * @param array $options
     * @param array $expected
     * @return void
     * @dataProvider dataProviderGetReportFiles
     */
    public function testGetReportFiles(array $options, array $expected)
    {
        $args = array_merge(array(__FILE__, __FILE__, 'text', 'codesize'), $options);
        $opts = new CommandLineOptions($args);

        $this->assertEquals($expected, $opts->getReportFiles());
    }

    public function dataProviderGetReportFiles()
    {
        return array(
            array(
                array('--reportfile-xml', __FILE__),
                array('xml' => __FILE__)
            ),
            array(
                array('--reportfile-html', __FILE__),
                array('html' => __FILE__)
            ),
            array(
                array('--reportfile-text', __FILE__),
                array('text' => __FILE__)
            ),
            array(
                array(
                    '--reportfile-text',
                    __FILE__,
                    '--reportfile-xml',
                    __FILE__,
                    '--reportfile-html',
                    __FILE__,
                ),
                array('text' => __FILE__, 'xml' => __FILE__, 'html' => __FILE__)
            ),
        );
    }
}
