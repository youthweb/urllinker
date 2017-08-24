<?php

namespace Youthweb\UrlLinker\Tests\Integration;

use PHPUnit\Framework\TestCase;

abstract class UrlLinkerTestCase extends TestCase
{
	/**
	 * @return array
	 */
	public function provideTextsNotContainingAnyUrls()
	{
		return array(
			array(''),
			array('Hello World!'),
			array('Looks like www.it.contains.an.url/somewhere but it really does not'),
			array('This german date 20.07.1963 isn\'t a domain.'), // @see https://bitbucket.org/kwi/urllinker/issues/23/german-dates-get-turned-into-links
		);
	}

	/**
	 * @return array
	 */
	public function provideTextsWithFtpLinksWithoutHtml()
	{
		return array(
			// simple
			array(
				'ftp://example.com',
				$this->link('ftp://example.com', 'example.com')
			),
		);
	}

	/**
	 * @return array
	 */
	public function provideTextsWithUppercaseLinksWithoutHtml()
	{
		return array(
			// simple
			array(
				'HTTP://EXAMPLE.COM',
				$this->link('HTTP://EXAMPLE.COM', 'EXAMPLE.COM')
			),
		);
	}

	/**
	 * @return array
	 */
	public function provideTextsWithLinksWithoutHtml()
	{
		return array(
			// simple
			array(
				'example.com',
				$this->link('http://example.com', 'example.com')
			),
			array(
				'http://example.com',
				$this->link('http://example.com', 'example.com'),
			),
			array(
				'https://example.com',
				$this->link('https://example.com', 'example.com'),
			),
			array(
				'www.example.com',
				$this->link('http://www.example.com', 'www.example.com'),
			),
			array(
				'http://www.example.com',
				$this->link('http://www.example.com', 'www.example.com'),
			),
			array(
				'https://www.example.com',
				$this->link('https://www.example.com', 'www.example.com'),
			),

			// with subdomain
			array(
				'subdomain.example.com',
				$this->link('http://subdomain.example.com', 'subdomain.example.com'),
			),

			// with resources
			array(
				'e.com/subdir',
				$this->link('http://e.com/subdir', 'e.com/subdir'),
			),
			array(
				'e.com/subdir/',
				$this->link('http://e.com/subdir/', 'e.com/subdir/'),
			),
			array(
				'e.com/subdir/resource.jpg',
				$this->link('http://e.com/subdir/resource.jpg', 'e.com/subdir/resource.jpg'),
			),

			// with query parameters
			array(
				'e.com?param1=val1',
				$this->link('http://e.com?param1=val1', 'e.com'),
			),
			array(
				'e.com?param1=val1&param2=val2',
				$this->link('http://e.com?param1=val1&amp;param2=val2', 'e.com'),
				'Should add protocol to link, replace "&" with "&amp;" and trim query parameters in contents',
			),

			// with resources and query parameters
			array(
				'e.com/subdir/resource.jpg?param1=val1&param2=val2',
				$this->link(
					'http://e.com/subdir/resource.jpg?param1=val1&amp;param2=val2',
					'e.com/subdir/resource.jpg'
				),
			),

			// with hash
			array(
				'e.com/test#hash',
				$this->link('http://e.com/test#hash', 'e.com/test'),
			),
			array(
				'e.com/test?p1=v1&p2=v2#hash',
				$this->link('http://e.com/test?p1=v1&amp;p2=v2#hash', 'e.com/test'),
			),

			// more than one link
			array(
				sprintf('%s foo bar %s', 'e1.com/t1', 'e2.com/t2'),
				sprintf(
					'%s foo bar %s',
					$this->link('http://e1.com/t1', 'e1.com/t1'),
					$this->link('http://e2.com/t2', 'e2.com/t2')
				),
			),

			// non-ascii characters
			// todo: shouldn't this get url-encoded to "http://e.com/%C5%BC%C3%B3%C5%82%C4%87"?
			array(
				'e.com/żółć',
				$this->link('http://e.com/żółć', 'e.com/żółć')
			),
			// german umlaute, @see https://bitbucket.org/kwi/urllinker/issues/13/special-characters-like-seems-break-it-up
			array(
				'visiüble www.pc.fi hidden ö hidden a.bc visibleä',
				sprintf(
					'visiüble %s hidden ö hidden a.bc visibleä',
					$this->link('http://www.pc.fi', 'www.pc.fi')
				),
			),

			// url-encoded url
			array(
				'e.com/%C5%BC%C3%B3%C5%82%C4%87',
				$this->link('http://e.com/%C5%BC%C3%B3%C5%82%C4%87', 'e.com/%C5%BC%C3%B3%C5%82%C4%87'),
				'Url should not be double encoded'
			),
		);
	}

	/**
	 * @param string $href
	 * @param string $content
	 * @return string
	 */
	protected function link($href, $content)
	{
		return sprintf('<a href="%s">%s</a>', $href, $content);
	}
}
