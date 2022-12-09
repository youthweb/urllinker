<?php

declare(strict_types=1);
/*
 * UrlLinker converts any web addresses in plain text into HTML hyperlinks.
 * Copyright (C) 2016-2022  Youthweb e.V. <info@youthweb.net>

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

class UrlLinkerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the default HtmlLinkCreator
     */
    public function testDefaultHtmlLinkCreator(): void
    {
        $urlLinker = new UrlLinker();

        $text = 'example.com';
        $expected = '<a href="http://example.com">example.com</a>';

        $this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
    }

    /**
     * Test a custom HtmlLinkCreator
     */
    public function testCustomHtmlLinkCreator(): void
    {
        // Simple htmlLinkCreator
        $creator = function ($url, $content) {
            return '<a href="' . $url . '" target="_blank">' . $content . '</a>';
        };

        $urlLinker = new UrlLinker([
            'htmlLinkCreator' => $creator,
        ]);

        $text = 'example.com';
        $expected = '<a href="http://example.com" target="_blank">example.com</a>';

        $this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
    }

    /**
     * Test a custom HtmlLinkCreator as deprecated callable
     */
    public function testCustomHtmlLinkCreatorAsCallable(): void
    {
        $urlLinker = new UrlLinker([
            'htmlLinkCreator' => [$this, 'customHtmlLinkCreatorCallable'],
        ]);

        $text = 'example.com';
        $expected = '<a href="http://example.com" target="_blank">example.com</a>';

        $this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
    }

    /**
     * Simple htmlLinkCreator used as callable for tests
     */
    public function customHtmlLinkCreatorCallable(string $url, string $content): string
    {
        return '<a href="' . $url . '" target="_blank">' . $content . '</a>';
    }


    /**
     * Test the default EmailLinkCreator
     */
    public function testDefaultEmailLinkCreator(): void
    {
        $urlLinker = new UrlLinker();

        $text = 'mail@example.com';
        $expected = '<a href="mailto:mail&#64;example.com">mail&#64;example.com</a>';

        $this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
    }

    /**
     * Test a custom EmailLinkCreator
     */
    public function testCustomEmailLinkCreator(): void
    {
        // Simple EmailLinkCreator
        $creator = function ($email, $content) {
            return '<a href="' . $email . '" class="email">' . $content . '</a>';
        };

        $urlLinker = new UrlLinker([
            'emailLinkCreator' => $creator,
        ]);

        $text = 'mail@example.com';
        $expected = '<a href="mail@example.com" class="email">mail@example.com</a>';

        $this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
    }

    /**
     * Test disable EmailLinkCreator
     */
    public function testDisableEmailLinkCreator(): void
    {
        // This EmailLinkCreator returns simply the email
        $creator = function ($email, $content) {
            return $email;
        };

        $urlLinker = new UrlLinker([
            'emailLinkCreator' => $creator,
        ]);

        $text = 'mail@example.com';
        $expected = 'mail@example.com';

        $this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
    }

    /**
     * Test a custom EmailLinkCreator provided as callable
     */
    public function testCustomEmailLinkCreatorAsCallable(): void
    {
        $urlLinker = new UrlLinker([
            'emailLinkCreator' => [$this, 'customEmailLinkCreatorCallable'],
        ]);

        $text = 'mail@example.com';
        $expected = '<a href="mail@example.com" class="email">mail@example.com</a>';

        $this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
    }

    /**
     * Simple emailLinkCreator used as callable for tests
     */
    public function customEmailLinkCreatorCallable(string $email, string $content): string {
        return '<a href="' . $email . '" class="email">' . $content . '</a>';
    }

    /**
     * Test html escaping
     *
     * @dataProvider providerEscapingHtml
     */
    public function testEscapingHtml(string $text, string $expected): void
    {
        $urlLinker = new UrlLinker();

        $this->assertSame($expected, $urlLinker->linkUrlsAndEscapeHtml($text));
    }

    /**
     * @return array<int,array<int,string>>
     */
    public function providerEscapingHtml(): array
    {
        return [
            [
                '\'',
                '\'',
            ],
            [
                '"',
                '&quot;',
            ],
            [
                '&quot;',
                '&quot;',
            ],
            [
                '<>',
                '&lt;&gt;',
            ],
            [
                '&lt;&gt;',
                '&lt;&gt;',
            ],
            [
                '&',
                '&amp;',
            ],
            [
                '&amp;',
                '&amp;',
            ],
        ];
    }
}
