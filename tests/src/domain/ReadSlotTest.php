<?php

namespace App\Tests\src\domain;


use App\Domain\ReadSlot\AttemptedReadUnexistingSecret;
use App\Domain\ReadSlot\ReadSlot;
use App\Domain\ReadSlot\SecretWasRead;
use App\Domain\ReadSlot\SecretWasWrittenInReadSlot;
use App\Domain\ReadSlot\UsedWrongPasswordWhenReading;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ReadSlotTest extends TestCase
{
	/**
	 * @test
	 */
	public function return_null_if_wrong_password()
	{
		$read = (new ReadSlot(
			Uuid::uuid4()->toString(),
			'sesamo1234'
		))->setSecret('this is my secret');

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
		$read = (new ReadSlot(
			Uuid::uuid4()->toString(),
			'sesamo1234'
		))->setSecret('this is my secret');

		$this->assertEquals(
			'this is my secret',
			$read->getSecret('sesamo1234')
		);

		$events = $read->getEvents();
		$this->assertCount(2, $events);
		$this->assertInstanceOf(SecretWasWrittenInReadSlot::class, $events[0]);
		$this->assertInstanceOf(SecretWasRead::class, $events[1]);
	}

	/**
	 * @test
	 */
	public function cannot_read_anything_if_no_secret_exists()
	{
		$read = new ReadSlot(
			Uuid::uuid4()->toString(),
			'sesamo1234'
		);

		$this->assertNull($read->getSecret('wrong password'));

		$events = $read->getEvents();
		$this->assertInstanceOf(AttemptedReadUnexistingSecret::class, $events[0]);
	}
}