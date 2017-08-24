<?php

namespace Youthweb\UrlLinker\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Youthweb\UrlLinker\UrlLinker;

class UrlLinkerTest extends TestCase
{
	/**
	 * @test UrlLinker implements UrlLinkerInterface
	 */
	public function testItImplementsUrlLinkerInterface()
	{
		$urlLinker = new UrlLinker();

		$this->assertInstanceOf('Youthweb\UrlLinker\UrlLinkerInterface', $urlLinker);

		// @deprecated since version 1.1, to be removed in 2.0.
		$this->assertFalse($urlLinker->getAllowFtpAddresses());
		// @deprecated since version 1.1, to be removed in 2.0.
		$this->assertFalse($urlLinker->getAllowUpperCaseUrlSchemes());
	}

	/**
	 * @test UrlLinker throws Exception with wrong htmlLinkCreator
	 */
	public function throwExceptionWithWrongHtmllinkcreator()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('The value of the htmlLinkCreator option must be callable.');

		$config = [
			'htmlLinkCreator' => 'this must be a Closure',
		];

		$urlLinker = new UrlLinker($config);
	}

	/**
	 * @test UrlLinker throws Exception with wrong emailLinkCreator
	 */
	public function throwExceptionWithWrongEmaillinkcreator()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('The value of the emailLinkCreator option must be callable.');

		$config = [
			'emailLinkCreator' => 'this must be a Closure',
		];

		$urlLinker = new UrlLinker($config);
	}

	/**
	 * @test Closures are allowed as htmlLinkCreator
	 */
	public function allowClosureAsHtmllinkcreator()
	{
		$config = [
			'htmlLinkCreator' => function() {},
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
	public function allowClosureAsEmaillinkcreator()
	{
		$config = [
			'emailLinkCreator' => function() {},
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
	public function allowCallableAsHtmllinkcreator()
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
	public function allowCallableAsEmaillinkcreator()
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
	public function testAllowFtpAddressesConfig()
	{
		$urlLinker = new UrlLinker();

		$this->assertFalse($urlLinker->getAllowFtpAddresses());

		$urlLinker->setAllowFtpAddresses(true);

		$this->assertTrue($urlLinker->getAllowFtpAddresses());
	}

	/**
	 * @deprecated since version 1.1, to be removed in 2.0.
	 */
	public function testAllowUpperCaseUrlSchemesConfig()
	{
		$urlLinker = new UrlLinker();

		$this->assertFalse($urlLinker->getAllowUpperCaseUrlSchemes());

		$urlLinker->setAllowUpperCaseUrlSchemes(true);

		$this->assertTrue($urlLinker->getAllowUpperCaseUrlSchemes());
	}

	/**
	 * @deprecated since version 1.1, to be removed in 2.0.
	 */
	public function testValidTldsConfig()
	{
		$urlLinker = new UrlLinker();

		$domains = ['.com' => true, '.org' => true];

		$urlLinker->setValidTlds($domains);

		$this->assertSame($domains, $urlLinker->getValidTlds());
	}

	/**
	 * Test that getHtmlLinkCreator() allways returns a closure
	 *
	 * @deprecated since version 1.1, to be removed in 2.0.
	 */
	public function testGetHtmlLinkCreator()
	{
		$urlLinker = new UrlLinker();

		$this->assertInstanceOf(\Closure::class, $urlLinker->getHtmlLinkCreator());
	}

	/**
	 * Test that a closure can be set
	 *
	 * @deprecated since version 1.1, to be removed in 2.0.
	 */
	public function testSetHtmlLinkCreator()
	{
		$urlLinker = new UrlLinker();

		// Simple htmlLinkCreator
		$creator = function($url, $content)
		{
			return '<a href="' . $url . '">' . $content . '</a>';
		};

		$urlLinker->setHtmlLinkCreator($creator);

		// Test that getHtmlLinkCreator() returns allways a closure
		$this->assertSame($creator, $urlLinker->getHtmlLinkCreator());
	}

	/**
	 * Test that getEmailLinkCreator() allways returns a closure
	 *
	 * @deprecated since version 1.1, to be removed in 2.0.
	 */
	public function testGetEmailLinkCreator()
	{
		$urlLinker = new UrlLinker();

		$this->assertInstanceOf(\Closure::class, $urlLinker->getEmailLinkCreator());
	}

	/**
	 * Test that a closure can be set
	 *
	 * @deprecated since version 1.1, to be removed in 2.0.
	 */
	public function testSetEmailLinkCreator()
	{
		$urlLinker = new UrlLinker();

		// Simple emailLinkCreator
		$creator = function($email, $content)
		{
			return '<a href="mailto:' . $email . '">' . $content . '</a>';
		};

		$urlLinker->setEmailLinkCreator($creator);

		// Test that getEmailLinkCreator() returns allways a closure
		$this->assertSame($creator, $urlLinker->getEmailLinkCreator());
	}

	/**
	 * @deprecated since version 1.1, to be removed in 2.0.
	 */
	public function testAllowingFtpAddresses()
	{
		$urlLinker = new UrlLinker();
		$urlLinker->setAllowFtpAddresses(true);
		$urlLinker->setValidTlds(['.com' => true]);

		$this->assertTrue($urlLinker->getAllowFtpAddresses());
		$this->assertFalse($urlLinker->getAllowUpperCaseUrlSchemes());

		$text = '<div>ftp://example.com</div>';
		$expectedText = '&lt;div&gt;<a href="ftp://example.com">example.com</a>&lt;/div&gt;';

		$this->assertSame($expectedText, $urlLinker->linkUrlsAndEscapeHtml($text));

		$html = '<div>ftp://example.com</div>';
		$expectedHtml = '<div><a href="ftp://example.com">example.com</a></div>';

		$this->assertSame($expectedHtml, $urlLinker->linkUrlsInTrustedHtml($html));
	}

	/**
	 * @deprecated since version 1.1, to be removed in 2.0.
	 */
	public function testAllowingUpperCaseSchemes()
	{
		$urlLinker = new UrlLinker();
		$urlLinker->setAllowUpperCaseUrlSchemes(true);
		$urlLinker->setValidTlds(['.com' => true]);

		$this->assertFalse($urlLinker->getAllowFtpAddresses());
		$this->assertTrue($urlLinker->getAllowUpperCaseUrlSchemes());

		$text = '<div>HTTP://example.com</div>';
		$expectedText = '&lt;div&gt;<a href="HTTP://example.com">example.com</a>&lt;/div&gt;';

		$this->assertSame($expectedText, $urlLinker->linkUrlsAndEscapeHtml($text));

		$html = '<div>HTTP://example.com</div>';
		$expectedHtml = '<div><a href="HTTP://example.com">example.com</a></div>';

		$this->assertSame($expectedHtml, $urlLinker->linkUrlsInTrustedHtml($html));
	}
}
