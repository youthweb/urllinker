<?php
/*
 * UrlLinker converts any web addresses in plain text into HTML hyperlinks.
 * Copyright (C) 2016-2019  Youthweb e.V. <info@youthweb.net>

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Youthweb\UrlLinker\Tests\Integration;

use Youthweb\UrlLinker\UrlLinker;

class UrlLinkerInTrustedHtmlTest extends UrlLinkerTestCase
{
    /**
     * @var UrlLinker
     */
    private $urlLinker;

    protected function setUp()
    {
        $this->urlLinker = new UrlLinker();
    }

    /**
     * @dataProvider provideTextsWithFtpLinksWithoutHtml
     *
     * @param string     $text
     * @param mixed      $expectedLinked
     * @param null|mixed $message
     */
    public function testFtpUrlsGetLinkedInText($text, $expectedLinked, $message = null)
    {
        $this->urlLinker = new UrlLinker([
            'allowFtpAddresses' => true,
        ]);

        $this->testUrlsGetLinkedInText($text, $expectedLinked, $message);
    }

    /**
     * @dataProvider provideTextsWithUppercaseLinksWithoutHtml
     *
     * @param string     $text
     * @param mixed      $expectedLinked
     * @param null|mixed $message
     */
    public function testUppercaseUrlsGetLinkedInText($text, $expectedLinked, $message = null)
    {
        $this->urlLinker = new UrlLinker([
            'allowUpperCaseUrlSchemes' => true,
        ]);

        $this->testUrlsGetLinkedInText($text, $expectedLinked, $message);
    }

    /**
     * @dataProvider provideTextsNotContainingAnyUrls
     *
     * @param string $text
     */
    public function testTextNotContainingAnyUrlsRemainsTheSame($text)
    {
        $this->assertSame($text, $this->urlLinker->linkUrlsInTrustedHtml($text));
    }

    public function testExample()
    {
        $html = <<<EOD
<p>Send me an <a href="bob@example.com">e-mail</a>
at bob@example.com.</p>
<p>This is already a link: <a href="http://google.com">http://google.com</a></p>
<p title='10>20'>Tricky markup...</p>
EOD;

        $expected = <<<EOD
<p>Send me an <a href="bob@example.com">e-mail</a>
at <a href="mailto:bob&#64;example.com">bob&#64;example.com</a>.</p>
<p>This is already a link: <a href="http://google.com">http://google.com</a></p>
<p title='10>20'>Tricky markup...</p>
EOD;

        $this->assertSame($expected, $this->urlLinker->linkUrlsInTrustedHtml($html));
    }

    /**
     * @dataProvider provideTextsWithLinksWithoutHtml
     *
     * @param string      $text
     * @param string      $expectedLinked
     * @param string|null $message
     */
    public function testUrlsGetLinkedInText($text, $expectedLinked, $message = null)
    {
        $this->assertSame(
            $expectedLinked,
            $this->urlLinker->linkUrlsInTrustedHtml($text),
            'Simple case: ' . $message
        );

        $this->assertSame(
            sprintf('foo %s bar', $expectedLinked),
            $this->urlLinker->linkUrlsInTrustedHtml(sprintf('foo %s bar', $text)),
            'Text around: ' . $message
        );

        // html should NOT get encoded
        $this->assertSame(
            sprintf('<div class="test">%s</div>', $expectedLinked),
            $this->urlLinker->linkUrlsInTrustedHtml(sprintf('<div class="test">%s</div>', $text)),
            'Html around: ' . $message
        );
    }

    /**
     * @dataProvider provideTextsWithHtml
     *
     * @param string      $text
     * @param string      $expectedLinked
     * @param string|null $message
     */
    public function testHtmlInText($text, $expectedLinked, $message = null)
    {
        $this->urlLinker = new UrlLinker([
            'allowUpperCaseUrlSchemes' => true,
        ]);

        $this->testUrlsGetLinkedInText($text, $expectedLinked);
    }

    /**
     * provide html in text
     */
    public function provideTextsWithHtml()
    {
        return [
            [
                '<a href="http://example.com?a=b&amp;c=d">example.com</a>',
                '<a href="http://example.com?a=b&amp;c=d">example.com</a>',
            ],
            [
                '<a href="http://example.com?a=b&amp%3Bc=d">example.com</a>',
                '<a href="http://example.com?a=b&amp%3Bc=d">example.com</a>',
            ],
            [
                '<a href="http://example.com?a=b%26amp%3Bc=d">example.com</a>',
                '<a href="http://example.com?a=b%26amp%3Bc=d">example.com</a>',
            ],
            [
                'http://example.com?a=b&c=d',
                $this->link('http://example.com?a=b&amp;c=d', 'example.com'),
            ],
            [
                'http://example.com?a=b&amp%3bc=d',
                $this->link('http://example.com?a=b&amp;amp%3bc=d', 'example.com'),
            ],
            [
                'http://example.com?a=b&amp;c=d',
                $this->link('http://example.com?a=b', 'example.com') . '&amp;c=d',
            ],
        ];
    }
}
