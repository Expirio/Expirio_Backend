<?php

namespace App\Tests\src\Infrastructure;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;
use App\Infrastructure\Mapper;
use PHPUnit\Framework\TestCase;

/**
 * @group infrastructure
 */
class MapperToDomainTest extends TestCase
{
	/**
	 * @test
	 */
	public function read_to_domain()
	{
		$givenDataForReadGuid = [
			'password' => 'mypassword',
			'attempts' => 2,
			'secret' => null
		];

		$readSlot = Mapper::toDomain('guid1', $givenDataForReadGuid);

		$this->assertInstanceOf(ReadSlot::class, $readSlot);
	}

	/**
	 * @test
	 */
	public function write_to_domain()
	{
		$givenDataForReadGuid = ['read_slot' => 'guid2'];

		$writeSlot = Mapper::toDomain('guid1', $givenDataForReadGuid);

		$this->assertInstanceOf(WriteSlot::class, $writeSlot);
	}
}