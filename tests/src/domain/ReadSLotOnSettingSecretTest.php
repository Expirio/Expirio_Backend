<?php

namespace App\Tests\src\domain;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\ReadSlot\SecretWasWrittenInReadSlot;
use App\Domain\ReadSlot\UsedWrongPasswordWhenReading;
use App\Tests\src\domain\builders\ReadSlotBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @group domain
 */
class ReadSLotOnSettingSecretTest extends TestCase
{
	/** @var ReadSlot */
	private $readSlot;

	public function setUp()
	{
		$this->readSlot = ReadSlotBuilder::anyWithNoSecret()
			->withPassword('sesamo1234')
			->withAmountOfFailures(0)
			->build();
	}

	/**
	 * @test
	 */
	public function when_set_then_data_is_protected()
	{
		$this->readSlot->setSecret('this is my secret');

		$this->assertTrue($this->readSlot->getPassword() !== 'sesamo1234', 'password is hashed after setting the secret');
		$this->assertTrue($this->readSlot->getSecret() !== 'this is my secret', 'secret is encoding after setting the secret');
	}

	/**
	 * @test
	 */
	public function event_when_secret_is_set()
	{
		$this->readSlot->setSecret('this is my secret');

		$events = $this->readSlot->getEvents();
		$this->assertInstanceOf(SecretWasWrittenInReadSlot::class, $events[0]);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage Secret cannot be set more than once. This is a violation
	 */
	public function secret_cannot_be_set_more_than_once()
	{
		$this->readSlot->setSecret('this is my secret');
		$this->readSlot->setSecret('this is my secret again');
	}
}