<?php

namespace App\Tests\src\domain;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\ReadSlot\SecretWasWrittenInReadSlot;
use App\Domain\ReadSlot\UsedWrongPasswordWhenReading;
use PHPUnit\Framework\TestCase;

/**
 * @group domain
 */
class ReadSlotTest extends TestCase
{
	/** @var ReadSlot */
	private $readSlot;

	public function setUp()
	{
		$this->readSlot = new ReadSlot('uid1', 'sesamo1234');
	}

	// setting the secret....

	/**
	 * @test
	 */
	public function when_secret_is_set_then_data_is_protected()
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

	// decrypting the secret....

	/**
	 * @test
	 */
	public function can_decrypt_secret_with_proper_password()
	{
		$decryptedSecret = $this->readSlot
			->setSecret('this is my secret')
			->revealSecret('sesamo1234');

		$this->assertEquals('this is my secret', $decryptedSecret);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage  Invalid password
	 */
	public function exception_is_thrown_is_invalid_password_is_used()
	{
		$this->readSlot
			->setSecret('this is my secret')
			->revealSecret('wrong passwod');
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage  Maximum attempts reached with wrong password
	 */
	public function limit_attemps_to_decrypt_work_as_expected()
	{
		$readSlot = new ReadSlot('uid1', 'sesamo1234', 'encrypted text here',  3);
		$readSlot->revealSecret('wrong passwod 1');
	}
}