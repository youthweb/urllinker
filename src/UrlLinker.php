<?php

namespace Youthweb\UrlLinker;

final class UrlLinker implements UrlLinkerInterface
{
	/**
	 * @var bool
	 */
	private $allowFtpAddresses = false;

	/**
	 * @var bool
	 */
	private $allowUpperCaseUrlSchemes = false;

	/**
	 * @var Closure
	 */
	private $htmlLinkCreator;

	/**
	 * @var Closure
	 */
	private $emailLinkCreator;

	/**
	 * @var array
	 */
	private $validTlds;

	/**
	 * @param bool $allowFtpAddresses
	 * @return self
	 */
	public function setAllowFtpAddresses($allowFtpAddresses)
	{
		$this->allowFtpAddresses = (bool) $allowFtpAddresses;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getAllowFtpAddresses()
	{
		return $this->allowFtpAddresses;
	}

	/**
	 * @param bool $allowUpperCaseUrlSchemes
	 * @return self
	 */
	public function setAllowUpperCaseUrlSchemes($allowUpperCaseUrlSchemes)
	{
		$this->allowUpperCaseUrlSchemes = (bool) $allowUpperCaseUrlSchemes;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getAllowUpperCaseUrlSchemes()
	{
		return $this->allowUpperCaseUrlSchemes;
	}

	/**
	 * @param Closure $creator
	 * @return self
	 */
	public function setHtmlLinkCreator(\Closure $creator)
	{
		$this->htmlLinkCreator = $creator;

		return $this;
	}

	/**
	 * @return Closure
	 */
	public function getHtmlLinkCreator()
	{
		if ( $this->htmlLinkCreator === null )
		{
			$this->htmlLinkCreator = function($url, $content)
			{
				return $this->createHtmlLink($url, $content);
			};
		}

		return $this->htmlLinkCreator;
	}

	/**
	 * @param Closure $creator
	 * @return self
	 */
	public function setEmailLinkCreator(\Closure $creator)
	{
		$this->emailLinkCreator = $creator;

		return $this;
	}

	/**
	 * @return Closure
	 */
	public function getEmailLinkCreator()
	{
		if ( $this->emailLinkCreator === null )
		{
			$this->emailLinkCreator = function($url, $content)
			{
				return $this->createEmailLink($url, $content);
			};
		}

		return $this->emailLinkCreator;
	}

	/**
	 * @param array $validTlds
	 * @return self
	 */
	public function setValidTlds(array $validTlds)
	{
		$this->validTlds = $validTlds;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getValidTlds()
	{
		if ( $this->validTlds === null )
		{
			$this->validTlds = DomainStorage::getValidTlds();
		}

		return $this->validTlds;
	}

	/**
	 * Transforms plain text into valid HTML, escaping special characters and
	 * turning URLs into links.
	 *
	 * @param string $text
	 * @return string
	 */
	public function linkUrlsAndEscapeHtml($text)
	{
		// We can abort if there is no . in $text
		if ( strpos($text, '.') === false )
		{
			return $this->escapeHtml($text);
		}

		$html = '';

		$position = 0;

		$match = array();

		while (preg_match($this->buildRegex(), $text, $match, PREG_OFFSET_CAPTURE, $position))
		{
			list($url, $urlPosition) = $match[0];

			// Add the text leading up to the URL.
			$html .= $this->escapeHtml(substr($text, $position, $urlPosition - $position));

			$scheme      = $match[1][0];
			$username    = $match[2][0];
			$password    = $match[3][0];
			$domain      = $match[4][0];
			$afterDomain = $match[5][0]; // everything following the domain
			$port        = $match[6][0];
			$path        = $match[7][0];

			// Check that the TLD is valid or that $domain is an IP address.
			$tld = strtolower(strrchr($domain, '.'));

			$validTlds = $this->getValidTlds();

			if (preg_match('{^\.[0-9]{1,3}$}', $tld) || isset($validTlds[$tld]))
			{
				// Do not permit implicit scheme if a password is specified, as
				// this causes too many errors (e.g. "my email:foo@example.org").
				if ( ! $scheme && $password )
				{
					$html .= $this->escapeHtml($username);

					// Continue text parsing at the ':' following the "username".
					$position = $urlPosition + strlen($username);

					continue;
				}

				if ( ! $scheme && $username && ! $password && ! $afterDomain )
				{
					// Looks like an email address.
					$emailLinkCreator = $this->getEmailLinkCreator();

					// Add the hyperlink.
					$html .= $emailLinkCreator($url, $url);
				}
				else
				{
					// Prepend http:// if no scheme is specified
					$completeUrl = $scheme ? $url : "http://$url";
					$linkText = "$domain$port$path";

					$htmlLinkCreator = $this->getHtmlLinkCreator();

					// Add the hyperlink.
					$html .= $htmlLinkCreator($completeUrl, $linkText);
				}
			}
			else
			{
				// Not a valid URL.
				$html .= $this->escapeHtml($url);
			}

			// Continue text parsing from after the URL.
			$position = $urlPosition + strlen($url);
		}

		// Add the remainder of the text.
		$html .= $this->escapeHtml(substr($text, $position));

		return $html;
	}

	/**
	 * Turns URLs into links in a piece of valid HTML/XHTML.
	 *
	 * Beware: Never render HTML from untrusted sources. Rendering HTML provided by
	 * a malicious user can lead to system compromise through cross-site scripting.
	 *
	 * @param string $html
	 * @return string
	 */
	public function linkUrlsInTrustedHtml($html)
	{
		$reMarkup = '{</?([a-z]+)([^"\'>]|"[^"]*"|\'[^\']*\')*>|&#?[a-zA-Z0-9]+;|$}';

		$insideAnchorTag = false;
		$position = 0;
		$result = '';

		// Iterate over every piece of markup in the HTML.
		while (true)
		{
			$match = array();
			preg_match($reMarkup, $html, $match, PREG_OFFSET_CAPTURE, $position);

			list($markup, $markupPosition) = $match[0];

			// Process text leading up to the markup.
			$text = substr($html, $position, $markupPosition - $position);

			// Link URLs unless we're inside an anchor tag.
			if ( ! $insideAnchorTag )
			{
				$text = $this->linkUrlsAndEscapeHtml($text);
			}

			$result .= $text;

			// End of HTML?
			if ( $markup === '' )
			{
				break;
			}

			// Check if markup is an anchor tag ('<a>', '</a>').
			if ( $markup[0] !== '&' && $match[1][0] === 'a' )
			{
				$insideAnchorTag = ($markup[1] !== '/');
			}

			// Pass markup through unchanged.
			$result .= $markup;

			// Continue after the markup.
			$position = $markupPosition + strlen($markup);
		}

		return $result;
	}

	/**
	 * @return string
	 */
	private function buildRegex()
	{
		/**
		 * Regular expression bits used by linkUrlsAndEscapeHtml() to match URLs.
		 */
		$rexScheme = 'https?://';

		if ( $this->getAllowFtpAddresses() )
		{
			$rexScheme .= '|ftp://';
		}

		$rexDomain     = '(?:[-a-zA-Z0-9\x7f-\xff]{1,63}\.)+[a-zA-Z\x7f-\xff][-a-zA-Z0-9\x7f-\xff]{1,62}';
		$rexIp         = '(?:[1-9][0-9]{0,2}\.|0\.){3}(?:[1-9][0-9]{0,2}|0)';
		$rexPort       = '(:[0-9]{1,5})?';
		$rexPath       = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
		$rexQuery      = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
		$rexFragment   = '(#[!$-/0-9?:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
		$rexUsername   = '[^]\\\\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64}';
		$rexPassword   = $rexUsername; // allow the same characters as in the username
		$rexUrl        = "($rexScheme)?(?:($rexUsername)(:$rexPassword)?@)?($rexDomain|$rexIp)($rexPort$rexPath$rexQuery$rexFragment)";
		$rexTrailPunct = "[)'?.!,;:]"; // valid URL characters which are not part of the URL if they appear at the very end
		$rexNonUrl	 = "[^-_#$+.!*%'(),;/?:@=&a-zA-Z0-9\x7f-\xff]"; // characters that should never appear in a URL

		$rexUrlLinker = "{\\b$rexUrl(?=$rexTrailPunct*($rexNonUrl|$))}";

		if ( $this->getAllowUpperCaseUrlSchemes() )
		{
			$rexUrlLinker .= 'i';
		}

		return $rexUrlLinker;
	}

	/**
	 * @param string $url
	 * @param string $content
	 * @return string
	 */
	private function createHtmlLink($url, $content)
	{
		$link = sprintf(
			'<a href="%s">%s</a>',
			$this->escapeHtml($url),
			$this->escapeHtml($content)
		);

		// Cheap e-mail obfuscation to trick the dumbest mail harvesters.
		return str_replace('@', '&#64;', $link);
	}

	/**
	 * @param string $url
	 * @param string $content
	 * @return string
	 */
	private function createEmailLink($url, $content)
	{
		$link = $this->createHtmlLink("mailto:$url", $content);

		// Cheap e-mail obfuscation to trick the dumbest mail harvesters.
		return str_replace('@', '&#64;', $link);
	}

	/**
	 * @param string $string
	 * @return string
	 */
	private function escapeHtml($string)
	{
		return htmlspecialchars($string);
	}
}
