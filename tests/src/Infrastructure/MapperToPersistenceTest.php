<?php

namespace App\Tests\src\Infrastructure;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;
use App\Infrastructure\Mapper;
use App\Tests\src\domain\builders\ReadSlotBuilder;
use App\Tests\src\domain\builders\WriteSlotBuilder;
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
		$givenSlot  = ReadSlotBuilder::anyWithNoSecret()
			->withPassword('password1')
			->withAmountOfFailures(0)
			->build();

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
	public function read_with_attempts_to_persistence()
	{
		$givenSlot  = ReadSlotBuilder::anyWithNoSecret()
			->withPassword('password1')
			->withSecret('this is my secret')
			->withAmountOfFailures(2)
			->build();

		$persistData = Mapper::toPersistence($givenSlot);

		$this->assertEquals([
			'password' => 'password1',
			'secret' => 'this is my secret',
			'attempts' => 2
		], $persistData);
	}

	/**
	 * @test
	 */
	public function write_to_persistence()
	{
		$givenSlot = WriteSlotBuilder::any()->withReadGuid('readguid1')->build();

		$persistData = Mapper::toPersistence($givenSlot);

		$this->assertEquals([
			'read_slot' => 'readguid1'
		], $persistData);
	}
}