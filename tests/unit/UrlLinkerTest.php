<?php

use Youthweb\UrlLinker\UrlLinker;

class UrlLinkerTest extends PHPUnit_Framework_TestCase
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