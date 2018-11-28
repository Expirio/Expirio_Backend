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
	 * @expectedException \Exception
	 * @expectedExceptionMessage  Invalid password
	 */
	public function exception_is_thrown_when_invalid_password()
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