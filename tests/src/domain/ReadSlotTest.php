<?php

namespace App\Tests\src\domain;

use App\Domain\ReadSlot;
use App\Domain\SecretWasRead;
use App\Domain\SecretWasWrittenInReadSlot;
use App\Domain\UsedWrongPasswordWhenReading;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ReadSlotTest extends TestCase
{
	/**
	 * @test
	 */
	public function return_null_if_wrong_password()
	{
		$read = ReadSlot::withPassword(
			Uuid::uuid4()->toString(),
			'sesamo1234'
		)->setSecret('this is my secret');

		$this->assertNull($read->getSecret('wrong password'));

		$events = $read->getEvents();
		$this->assertCount(2, $events);
		$this->assertInstanceOf(SecretWasWrittenInReadSlot::class, $events[0]);
		$this->assertInstanceOf(UsedWrongPasswordWhenReading::class, $events[1]);
	}

	/**
	 * @test
	 */
	public function can_decrypt_secret_with_proper_password()
	{
		$read = ReadSlot::withPassword(
			Uuid::uuid4()->toString(),
			'sesamo1234'
		)->setSecret('this is my secret');

		$this->assertEquals(
			'this is my secret',
			$read->getSecret('sesamo1234')
		);

		$events = $read->getEvents();
		$this->assertCount(2, $events);
		$this->assertInstanceOf(SecretWasWrittenInReadSlot::class, $events[0]);
		$this->assertInstanceOf(SecretWasRead::class, $events[1]);
	}
}