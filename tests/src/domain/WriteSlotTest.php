<?php

namespace App\Tests\src\domain;

use App\Domain\WriteSlot\WriteSlot;
use App\Domain\WriteSlot\WriteSlotWasWritten;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group domain
 */
class WriteSlotTest extends TestCase
{
	/**
	 * @test
	 */
	public function can_write_secret()
	{

		$write = new WriteSlot(
			Uuid::uuid4()->toString(),
			Uuid::uuid4()->toString()
		);

		$write->setSecret('my secret text');

		$events = $write->getEvents();
		$this->assertInstanceOf(WriteSlotWasWritten::class, $events[0]);
	}
}