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

	/**
	 * Test the default EmailLinkCreator
	 */
	public function testDefaultEmailLinkCreator()
	{
		$urlLinker = new UrlLinker();

		$text = 'mail@example.com';
		$expected = '<a href="mailto:mail&#64;example.com">mail&#64;example.com</a>';

		$this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
	}

	/**
	 * Test a custom EmailLinkCreator
	 */
	public function testCustomEmailLinkCreator()
	{
		$urlLinker = new UrlLinker();

		// Simple EmailLinkCreator
		$creator = function($email, $content)
		{
			return '<a href="' . $email . '" class="email">' . $content . '</a>';
		};

		$urlLinker->setEmailLinkCreator($creator);

		$text = 'mail@example.com';
		$expected = '<a href="mail@example.com" class="email">mail@example.com</a>';

		$this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
	}

	/**
	 * Test disable EmailLinkCreator
	 */
	public function testDisableEmailLinkCreator()
	{
		$urlLinker = new UrlLinker();

		// This EmailLinkCreator returns simply the email
		$creator = function($email, $content)
		{
			return $email;
		};

		$urlLinker->setEmailLinkCreator($creator);

		$text = 'mail@example.com';
		$expected = 'mail@example.com';

		$this->assertSame($expected, $urlLinker->linkUrlsInTrustedHtml($text));
	}

	/**
	 * Test html escaping
	 *
	 * @dataProvider providerEscapingHtml
	 */
	public function testEscapingHtml($text, $expected)
	{
		$urlLinker = new UrlLinker();

		$this->assertSame($expected, $urlLinker->linkUrlsAndEscapeHtml($text));
	}

	public function providerEscapingHtml()
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
