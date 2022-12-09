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

namespace Youthweb\UrlLinker\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Youthweb\UrlLinker\UrlLinker;

class UrlLinkerTest extends TestCase
{
    /**
     * @test UrlLinker implements UrlLinkerInterface
     */
    public function testItImplementsUrlLinkerInterface(): void
    {
        $urlLinker = new UrlLinker();

        $this->assertInstanceOf('Youthweb\UrlLinker\UrlLinkerInterface', $urlLinker);
    }

    /**
     * @test UrlLinker throws Exception with wrong htmlLinkCreator
     */
    public function throwExceptionWithWrongHtmllinkcreator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Option "htmlLinkCreator" must be of type "Closure", "string" given.');

        $config = [
            'htmlLinkCreator' => 'this must be a Closure',
        ];

        $urlLinker = new UrlLinker($config);
    }

    /**
     * @test UrlLinker throws Exception with wrong emailLinkCreator
     */
    public function throwExceptionWithWrongEmaillinkcreator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Option "emailLinkCreator" must be of type "Closure", "string" given.');

        $config = [
            'emailLinkCreator' => 'this must be a Closure',
        ];

        $urlLinker = new UrlLinker($config);
    }

    /**
     * @test Closures are allowed as htmlLinkCreator
     */
    public function allowClosureAsHtmllinkcreator(): void
    {
        $config = [
            'htmlLinkCreator' => function (): void {
            },
        ];

        $urlLinker = new UrlLinker($config);

        // Workaround to test that NO Exception is thrown
        // @see https://github.com/sebastianbergmann/phpunit-documentation/issues/171
        $this->assertTrue(
            true,
            'Return type ensures this assertion is never reached on failure'
        );
    }

    /**
     * @test Closures are allowed as emailLinkCreator
     */
    public function allowClosureAsEmaillinkcreator(): void
    {
        $config = [
            'emailLinkCreator' => function (): void {
            },
        ];

        $urlLinker = new UrlLinker($config);

        // Workaround to test that NO Exception is thrown
        // @see https://github.com/sebastianbergmann/phpunit-documentation/issues/171
        $this->assertTrue(
            true,
            'Return type ensures this assertion is never reached on failure'
        );
    }

    /**
     * @test Callables are allowed as htmlLinkCreator
     */
    public function allowCallableAsHtmllinkcreator(): void
    {
        $config = [
            'htmlLinkCreator' => [$this, '__construct'],
        ];

        $urlLinker = new UrlLinker($config);

        // Workaround to test that NO Exception is thrown
        // @see https://github.com/sebastianbergmann/phpunit-documentation/issues/171
        $this->assertTrue(
            true,
            'Return type ensures this assertion is never reached on failure'
        );
    }

    /**
     * @test Callables are allowed as emailLinkCreator
     */
    public function allowCallableAsEmaillinkcreator(): void
    {
        $config = [
            'emailLinkCreator' => [$this, '__construct'],
        ];

        $urlLinker = new UrlLinker($config);

        // Workaround to test that NO Exception is thrown
        // @see https://github.com/sebastianbergmann/phpunit-documentation/issues/171
        $this->assertTrue(
            true,
            'Return type ensures this assertion is never reached on failure'
        );
    }

    /**
     * @deprecated since version 1.1, to be removed in 2.0.
     */
    public function testValidTldsConfig(): void
    {
        $urlLinker = new UrlLinker();

        $domains = ['.com' => true, '.org' => true];

        $urlLinker->setValidTlds($domains);

        $this->assertSame($domains, $urlLinker->getValidTlds());
    }

    /**
     * Test that a closure can be set
     */
    public function testSettingHtmlLinkCreator(): void
    {
        // Simple htmlLinkCreator
        $creator = function ($url, $content) {
            return '<a href="' . $url . '">' . $content . '</a>';
        };

        $urlLinker = new UrlLinker([
            'htmlLinkCreator' => $creator,
        ]);

        // Workaround to test that NO Exception is thrown
        // @see https://github.com/sebastianbergmann/phpunit-documentation/issues/171
        $this->assertTrue(true);
    }

    /**
     * Test that getEmailLinkCreator() allways returns a closure
     *
     * @deprecated since version 1.1, to be removed in 2.0.
     */
    public function testGetEmailLinkCreator(): void
    {
        $urlLinker = new UrlLinker();

        $this->assertInstanceOf(\Closure::class, $urlLinker->getEmailLinkCreator());
    }

    /**
     * Test that a closure can be set
     *
     * @deprecated since version 1.1, to be removed in 2.0.
     */
    public function testSetEmailLinkCreator(): void
    {
        $urlLinker = new UrlLinker();

        // Simple emailLinkCreator
        $creator = function ($email, $content) {
            return '<a href="mailto:' . $email . '">' . $content . '</a>';
        };

        $urlLinker->setEmailLinkCreator($creator);

        // Test that getEmailLinkCreator() returns allways a closure
        $this->assertSame($creator, $urlLinker->getEmailLinkCreator());
    }

    public function testNotAllowingFtpAddresses(): void
    {
        $urlLinker = new UrlLinker([
            'allowFtpAddresses' => false,
        ]);
        $urlLinker->setValidTlds(['.com' => true]);

        $text = '<div>ftp://example.com</div>';
        $expectedText = '&lt;div&gt;ftp://<a href="http://example.com">example.com</a>&lt;/div&gt;';

        $this->assertSame($expectedText, $urlLinker->linkUrlsAndEscapeHtml($text));

        $html = '<div>ftp://example.com</div>';
        $expectedHtml = '<div>ftp://<a href="http://example.com">example.com</a></div>';

        $this->assertSame($expectedHtml, $urlLinker->linkUrlsInTrustedHtml($html));
    }

    public function testAllowingFtpAddresses(): void
    {
        $urlLinker = new UrlLinker([
            'allowFtpAddresses' => true,
        ]);
        $urlLinker->setValidTlds(['.com' => true]);

        $text = '<div>ftp://example.com</div>';
        $expectedText = '&lt;div&gt;<a href="ftp://example.com">example.com</a>&lt;/div&gt;';

        $this->assertSame($expectedText, $urlLinker->linkUrlsAndEscapeHtml($text));

        $html = '<div>ftp://example.com</div>';
        $expectedHtml = '<div><a href="ftp://example.com">example.com</a></div>';

        $this->assertSame($expectedHtml, $urlLinker->linkUrlsInTrustedHtml($html));
    }

    public function testNotAllowingUpperCaseSchemes(): void
    {
        $urlLinker = new UrlLinker([
            'allowUpperCaseUrlSchemes' => false,
        ]);
        $urlLinker->setValidTlds(['.com' => true]);

        $text = '<div>HTTP://example.com</div>';
        $expectedText = '&lt;div&gt;HTTP://<a href="http://example.com">example.com</a>&lt;/div&gt;';

        $this->assertSame($expectedText, $urlLinker->linkUrlsAndEscapeHtml($text));

        $html = '<div>HTTP://example.com</div>';
        $expectedHtml = '<div>HTTP://<a href="http://example.com">example.com</a></div>';

        $this->assertSame($expectedHtml, $urlLinker->linkUrlsInTrustedHtml($html));
    }

    public function testAllowingUpperCaseSchemes(): void
    {
        $urlLinker = new UrlLinker([
            'allowUpperCaseUrlSchemes' => true,
        ]);
        $urlLinker->setValidTlds(['.com' => true]);

        $text = '<div>HTTP://example.com</div>';
        $expectedText = '&lt;div&gt;<a href="HTTP://example.com">example.com</a>&lt;/div&gt;';

        $this->assertSame($expectedText, $urlLinker->linkUrlsAndEscapeHtml($text));

        $html = '<div>HTTP://example.com</div>';
        $expectedHtml = '<div><a href="HTTP://example.com">example.com</a></div>';

        $this->assertSame($expectedHtml, $urlLinker->linkUrlsInTrustedHtml($html));
    }
}
