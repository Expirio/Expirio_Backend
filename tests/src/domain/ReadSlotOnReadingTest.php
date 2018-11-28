<?php

namespace App\Tests\src\domain;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\ReadSlot\UsedWrongPasswordWhenReading;
use PHPUnit\Framework\TestCase;

/**
 * @group domain
 */
class ReadSlotOnReadingTest extends TestCase
{
	/** @var ReadSlot */
	private $readSlot;

	public function setUp()
	{
		$this->readSlot = new ReadSlot('uid1', 'sesamo1234');
	}

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
	 */
	public function null_returned_when_wrong_password()
	{
		$secret = $this->readSlot
			->setSecret('this is my secret')
			->revealSecret('wrong passwod');

		$this->assertNull($secret);
	}

	/**
	 * @test
	 */
	public function limit_attemps_to_decrypt_work_as_expected()
	{
		$readSlot = new ReadSlot('uid1', 'sesamo1234', 'encrypted text here',  2);

		$readSlot->revealSecret('wrong passwod 3');
		$this->assertEquals(3, $readSlot->getAmountOfAttempts());
		$readSlot->revealSecret('wrong passwod 4');
		$this->assertEquals(4, $readSlot->getAmountOfAttempts());
	}
}