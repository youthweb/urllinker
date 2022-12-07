<?php
/*
 * UrlLinker converts any web addresses in plain text into HTML hyperlinks.
 * Copyright (C) 2016-2019  Youthweb e.V. <info@youthweb.net>

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Youthweb\UrlLinker\Tests\Integration;

use PHPUnit\Framework\TestCase;

abstract class UrlLinkerTestCase extends TestCase
{
    /**
     * @return array<int,array<int,string>>
     */
    public function provideTextsNotContainingAnyUrls(): array
    {
        return [
            [''],
            ['Hello World!'],
            ['Looks like www.it.contains.an.url/somewhere but it really does not'],
            ['This german date 20.07.1963 isn\'t a domain.'], // @see https://bitbucket.org/kwi/urllinker/issues/23/german-dates-get-turned-into-links
        ];
    }

    /**
     * @return array<int,array<int,string>>
     */
    public function provideTextsWithFtpLinksWithoutHtml(): array
    {
        return [
            // simple
            [
                'ftp://example.com',
                $this->link('ftp://example.com', 'example.com')
            ],
        ];
    }

    /**
     * @return array<int,array<int,string>>
     */
    public function provideTextsWithUppercaseLinksWithoutHtml(): array
    {
        return [
            // simple
            [
                'HTTP://EXAMPLE.COM',
                $this->link('HTTP://EXAMPLE.COM', 'EXAMPLE.COM')
            ],
        ];
    }

    /**
     * @return array<int,array<int,string>>
     */
    public function provideTextsWithLinksWithoutHtml(): array
    {
        return [
            // simple
            [
                'example.com',
                $this->link('http://example.com', 'example.com')
            ],
            [
                'http://example.com',
                $this->link('http://example.com', 'example.com'),
            ],
            [
                'https://example.com',
                $this->link('https://example.com', 'example.com'),
            ],
            [
                'www.example.com',
                $this->link('http://www.example.com', 'www.example.com'),
            ],
            [
                'http://www.example.com',
                $this->link('http://www.example.com', 'www.example.com'),
            ],
            [
                'https://www.example.com',
                $this->link('https://www.example.com', 'www.example.com'),
            ],

            // with subdomain
            [
                'subdomain.example.com',
                $this->link('http://subdomain.example.com', 'subdomain.example.com'),
            ],

            // with resources
            [
                'e.com/subdir',
                $this->link('http://e.com/subdir', 'e.com/subdir'),
            ],
            [
                'e.com/subdir/',
                $this->link('http://e.com/subdir/', 'e.com/subdir/'),
            ],
            [
                'e.com/subdir/resource.jpg',
                $this->link('http://e.com/subdir/resource.jpg', 'e.com/subdir/resource.jpg'),
            ],

            // with query parameters
            [
                'e.com?param1=val1',
                $this->link('http://e.com?param1=val1', 'e.com'),
            ],
            [
                'e.com?param1=val1&param2=val2',
                $this->link('http://e.com?param1=val1&amp;param2=val2', 'e.com'),
                'Should add protocol to link, replace "&" with "&amp;" and trim query parameters in contents',
            ],

            // with resources and query parameters
            [
                'e.com/subdir/resource.jpg?param1=val1&param2=val2',
                $this->link(
                    'http://e.com/subdir/resource.jpg?param1=val1&amp;param2=val2',
                    'e.com/subdir/resource.jpg'
                ),
            ],

            // with hash
            [
                'e.com/test#hash',
                $this->link('http://e.com/test#hash', 'e.com/test'),
            ],
            [
                'e.com/test?p1=v1&p2=v2#hash',
                $this->link('http://e.com/test?p1=v1&amp;p2=v2#hash', 'e.com/test'),
            ],

            // more than one link
            [
                sprintf('%s foo bar %s', 'e1.com/t1', 'e2.com/t2'),
                sprintf(
                    '%s foo bar %s',
                    $this->link('http://e1.com/t1', 'e1.com/t1'),
                    $this->link('http://e2.com/t2', 'e2.com/t2')
                ),
            ],

            // non-ascii characters
            // todo: shouldn't this get url-encoded to "http://e.com/%C5%BC%C3%B3%C5%82%C4%87"?
            [
                'e.com/żółć',
                $this->link('http://e.com/żółć', 'e.com/żółć')
            ],
            // german umlaute, @see https://bitbucket.org/kwi/urllinker/issues/13/special-characters-like-seems-break-it-up
            [
                'visiüble www.pc.fi hidden ö hidden a.bc visibleä',
                sprintf(
                    'visiüble %s hidden ö hidden a.bc visibleä',
                    $this->link('http://www.pc.fi', 'www.pc.fi')
                ),
            ],

            // url-encoded url
            [
                'e.com/%C5%BC%C3%B3%C5%82%C4%87',
                $this->link('http://e.com/%C5%BC%C3%B3%C5%82%C4%87', 'e.com/%C5%BC%C3%B3%C5%82%C4%87'),
                'Url should not be double encoded'
            ],
        ];
    }

    /**
     * Create a HTML link from href and content
     */
    protected function link(string $href, string $content): string
    {
        return sprintf('<a href="%s">%s</a>', $href, $content);
    }
}
