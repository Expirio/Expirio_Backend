<?php

namespace App\Tests\src\Infrastructure;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;
use App\Infrastructure\Mapper;
use PHPUnit\Framework\TestCase;

/**
 * @group infrastructure
 */
class MapperToPersistenceTest extends TestCase
{
	/**
	 * @test
	 */
	public function read_to_persistence()
	{
		$givenSlot = new ReadSlot('guid1', 'password1');

		$persistData = Mapper::toPersistence($givenSlot);

		$this->assertEquals([
			'password' => 'password1',
			'secret' => null,
			'attempts' => 0
		], $persistData);
	}

	/**
	 * @test
	 */
	public function write_to_persistence()
	{
		$givenSlot = new WriteSlot('guid1', 'readguid1');

		$persistData = Mapper::toPersistence($givenSlot);

		$this->assertEquals([
			'read_slot' => 'readguid1'
		], $persistData);
	}
}