<?php

namespace App\Tests\src\domain;

use App\Domain\WriteSlot\WriteSlot;
use App\Domain\WriteSlot\WriteSlotWasWritten;
use PHPUnit\Framework\TestCase;

/**
 * @group domain
 */
class WriteSlotTest extends TestCase
{
	/** @var WriteSlot */
	private $writeSlot;

	public function setUp()
	{
		$this->writeSlot = new WriteSlot('uid1', 'uid2');
	}

	/**
	 * @test
	 */
	public function event_is_created_when_secret_is_set()
	{
		$this->writeSlot->setSecret('my secret text');

		$events = $this->writeSlot->getEvents();
		$this->assertInstanceOf(WriteSlotWasWritten::class, $events[0]);
	}
}