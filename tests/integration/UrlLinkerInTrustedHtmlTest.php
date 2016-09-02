<?php

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
	 * @param string	  $text
	 * @param string	  $expectedLinked
	 * @param string|null $message
	 */
	public function testUrlsGetLinkedInText($text, $expectedLinked, $message = null)
	{
		$this->assertSame(
			$expectedLinked,
			$this->urlLinker->linkUrlsInTrustedHtml($text),
			'Simple case: '.$message
		);

		$this->assertSame(
			sprintf('foo %s bar', $expectedLinked),
			$this->urlLinker->linkUrlsInTrustedHtml(sprintf('foo %s bar', $text)),
			'Text around: '.$message
		);

		// html should NOT get encoded
		$this->assertSame(
			sprintf('<div class="test">%s</div>', $expectedLinked),
			$this->urlLinker->linkUrlsInTrustedHtml(sprintf('<div class="test">%s</div>', $text)),
			'Html around: '.$message
		);
	}
}
