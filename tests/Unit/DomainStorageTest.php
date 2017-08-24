<?php

namespace Youthweb\UrlLinker\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Youthweb\UrlLinker\DomainStorage;

class DomainStorageTest extends TestCase
{
	public function testGetValidTlds()
	{
		$tlds = DomainStorage::getValidTlds();

		$this->assertCount(1547, $tlds);

		$this->assertSame(['.aaa' => true], array_slice($tlds, 0, 1));
	}
}
