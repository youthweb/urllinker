<?php

use Youthweb\UrlLinker\DomainStorage;

class DomainStorageTest extends PHPUnit_Framework_TestCase
{
	public function testGetValidTlds()
	{
		$tlds = DomainStorage::getValidTlds();

		$this->assertCount(1493, $tlds);

		$this->assertSame(['.aaa' => true], array_slice($tlds, 0, 1));
	}
}
