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

	public function testAllowingFtpAddresses()
	{
		$urlLinker = new UrlLinker();
		$urlLinker->setAllowFtpAddresses(true);

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
