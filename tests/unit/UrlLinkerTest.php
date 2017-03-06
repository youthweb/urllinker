<?php

namespace Youthweb\UrlLinker\Tests\Unit;

use Youthweb\UrlLinker\UrlLinker;

class UrlLinkerTest extends \PHPUnit\Framework\TestCase
{
	public function testItImplementsUrlLinkerInterface()
	{
		$urlLinker = new UrlLinker();

		$this->assertInstanceOf('Youthweb\UrlLinker\UrlLinkerInterface', $urlLinker);

		$this->assertFalse($urlLinker->getAllowFtpAddresses());
		$this->assertFalse($urlLinker->getAllowUpperCaseUrlSchemes());
	}

	public function testAllowFtpAddressesConfig()
	{
		$urlLinker = new UrlLinker();

		$this->assertFalse($urlLinker->getAllowFtpAddresses());

		$urlLinker->setAllowFtpAddresses(true);

		$this->assertTrue($urlLinker->getAllowFtpAddresses());
	}

	public function testAllowUpperCaseUrlSchemesConfig()
	{
		$urlLinker = new UrlLinker();

		$this->assertFalse($urlLinker->getAllowUpperCaseUrlSchemes());

		$urlLinker->setAllowUpperCaseUrlSchemes(true);

		$this->assertTrue($urlLinker->getAllowUpperCaseUrlSchemes());
	}

	public function testValidTldsConfig()
	{
		$urlLinker = new UrlLinker();

		$domains = ['.com' => true, '.org' => true];

		$urlLinker->setValidTlds($domains);

		$this->assertSame($domains, $urlLinker->getValidTlds());
	}

	/**
	 * Test that getHtmlLinkCreator() allways returns a closure
	 */
	public function testGetHtmlLinkCreator()
	{
		$urlLinker = new UrlLinker();

		$this->assertInstanceOf(\Closure::class, $urlLinker->getHtmlLinkCreator());
	}

	/**
	 * Test that a closure can be set
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
	 */
	public function testGetEmailLinkCreator()
	{
		$urlLinker = new UrlLinker();

		$this->assertInstanceOf(\Closure::class, $urlLinker->getEmailLinkCreator());
	}

	/**
	 * Test that a closure can be set
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
