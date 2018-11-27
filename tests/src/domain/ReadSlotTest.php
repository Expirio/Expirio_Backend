<?php

namespace App\Tests\src\domain;


use App\Domain\ReadSlot\AttemptedReadUnexistingSecret;
use App\Domain\ReadSlot\ReadSlot;
use App\Domain\ReadSlot\SecretWasRead;
use App\Domain\ReadSlot\SecretWasWrittenInReadSlot;
use App\Domain\ReadSlot\UsedWrongPasswordWhenReading;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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
	 */
	public function events_when_wrong_password_is_used()
	{
		$this->readSlot->setSecret('this is my secret');

		$this->readSlot->revealSecret('wrong password');
		$this->readSlot->revealSecret('wrong password again');


		$events = $this->readSlot->getEvents();
		$this->assertInstanceOf(UsedWrongPasswordWhenReading::class, $events[1]);
		$this->assertInstanceOf(UsedWrongPasswordWhenReading::class, $events[2]);
	}

	/**
	 * @test
	 */
	public function secret_is_null_if_wrong_password_is_used()
	{
		$decrypted = $this->readSlot
			->setSecret('this is my secret')
			->revealSecret('wrong passwod');

		$this->assertNull($decrypted);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage  Maximum attempts reached with wrong password
	 */
	public function limit_attemps_to_decrypt_work_as_expected()
	{
		$this->readSlot->setSecret('this is my secret');

		$this->readSlot->revealSecret('wrong passwod 1');
		$this->readSlot->revealSecret('wrong passwod 2');
		$this->readSlot->revealSecret('wrong passwod 3');
	}
}