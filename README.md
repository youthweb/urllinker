# UrlLinker

[![Latest Version](https://img.shields.io/github/release/youthweb/urllinker.svg)](https://github.com/youthweb/urllinker/releases)
[![Software License](https://img.shields.io/badge/license-GPL3-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/youthweb/urllinker.svg?branch=master)](https://travis-ci.org/youthweb/urllinker)
[![Coverage Status](https://coveralls.io/repos/github/youthweb/urllinker/badge.svg?branch=master)](https://coveralls.io/github/youthweb/bbcode-parser?branch=master)

UrlLinker converts any web addresses in plain text into HTML hyperlinks.

This is a fork of the great work of [Kwi\UrlLinker](https://bitbucket.org/kwi/urllinker).

## Install

Via Composer

```bash
$ composer require youthweb/urllinker
```

## Usage

```php
$urlLinker = new Youthweb\UrlLinker\UrlLinker();

$urlLinker->linkUrlsAndEscapeHtml($text);

$urlLinker->linkUrlsInTrustedHtml($html);
```

You can configure different options for parsing URLs by passing them into `UrlLinker`'s constructor:

```php
// Ftp addresses like "ftp://example.com" will be allowed:
$urlLinker = new Youthweb\UrlLinker\UrlLinker(true);

// Uppercase URL schemes like "HTTP://exmaple.com" will be allowed:
$urlLinker = new Youthweb\UrlLinker\UrlLinker(false, true);
```

## Recognized addresses

- Web addresses
  - Recognized URL schemes: "http" and "https"
    - The ``http://`` prefix is optional.
    - Support for additional schemes, e.g. "ftp", can easily be added by
      tweaking ``$rexScheme``.
    - The scheme must be written in lower case. This requirement can be lifted
      by adding an ``i`` (the ``PCRE_CASELESS`` modifier) to ``$rexUrlLinker``.
  - Hosts may be specified using domain names or IPv4 addresses.
    - IPv6 addresses are not supported.
  - Port numbers are allowed.
  - Internationalized Resource Identifiers (IRIs) are allowed. Note that the
    job of converting IRIs to URIs is left to the user's browser.
  - To reduce false positives, UrlLinker verifies that the top-level domain is
    on the official IANA list of valid TLDs.
    - UrlLinker is updated from time to time as the TLD list is expanded.
    - In the future, this approach may collapse under ICANN's ill-advised new
      policy of selling arbitrary TLDs for large amounts of cash, but for now
      it is an effective method of rejecting invalid URLs.
    - Internationalized *top-level* domain names must be written in Punycode in
      order to be recognized.
    - If you need to support unqualified domain names, such as ``localhost``,
      you may disable the TLD check by 1) replacing ``+`` with ``*`` in the
      ``$rexDomain`` value and 2) replacing the ``if`` statement line beneath
      the "Check that the TLD is valid" comment with ``if (true)``. This is
      obviously a quick-and-dirty hack, and may cause false positives.
- Email addresses
  - Supports the full range of commonly used address formats, including "plus
    addresses" (as popularized by Gmail).
  - Does not recognized the more obscure address variants that are allowed by
    the RFCs but never seen in practice.
  - Simplistic spam protection: The at-sign is converted to a HTML entity,
    foiling naive email address harvesters.
- Addresses are recognized correctly in normal sentence contexts. For instance,
  in "Visit stackoverflow.com.", the final period is not part of the URL.
- User input is properly sanitized to prevent `cross-site scripting`__ (XSS),
  and ampersands in URLs are `correctly escaped`__ as ``&amp;`` (this does not
  apply to the ``linkUrlsInTrustedHtml`` function, which assumes its input to
  be valid HTML).

__ http://en.wikipedia.org/wiki/Cross-site_scripting
__ http://www.htmlhelp.com/tools/validator/problems.html#amp

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Tests

Unit tests are written using [PHPUnit](https://phpunit.de).

```bash
$ phpunit
```

## Contributing

Please feel free to submit bugs or to fork and sending Pull Requests.

## License

GPL3. Please see [License File](LICENSE.md) for more information.
