<?php

namespace Youthweb\UrlLinker\Tests\Integration;

use Youthweb\UrlLinker\UrlLinker;

class UrlLinkerTest extends \PHPUnit_Framework_TestCase
{
	public function testGetValidTlds()
	{
		$urlLinker = new UrlLinker();

		$tlds = $urlLinker->getValidTlds();

		$this->assertTrue(is_array($tlds));
		$this->assertTrue(array_key_exists('.com', $tlds));
		$this->assertTrue($tlds['.com']);
	}

	/**
	 * Test the default HtmlLinkCreator
	 */
	public function testDefaultHtmlLinkCreator()
	{
		$urlLinker = new UrlLinker();

		$text = 'example.com';
		$expected = '<a href="http://example.com">example.com</a>';

		$this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
	}

	/**
	 * Test a custom HtmlLinkCreator
	 */
	public function testCustomHtmlLinkCreator()
	{
		$urlLinker = new UrlLinker();

		// Simple htmlLinkCreator
		$creator = function($url, $content)
		{
			return '<a href="' . $url . '" target="_blank">' . $content . '</a>';
		};

		$urlLinker->setHtmlLinkCreator($creator);

		$text = 'example.com';
		$expected = '<a href="http://example.com" target="_blank">example.com</a>';

		$this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
	}
}
