<?php

namespace App\Tests\src\domain\builders;

use App\Domain\ReadSlot\ReadSlot;
use Faker\Factory;
use Ramsey\Uuid\Uuid;

class ReadSlotBuilder
{
	private $guid;
	private $password;
	private $secret;
	private $amountFailedAttempts;
	private $expiration;

	public function any() {
		$faker = Factory::create();

		$this->guid = Uuid::uuid4()->toString();
		$this->password = $faker->word;
		$this->secret = $faker->sentence;
		$this->amountFailedAttempts = $faker->numberBetween(0, 2);
		$this->expiration = $faker->dateTimeThisYear;
	}

	public function withGuid($guid)
	{
		$this->guid = $guid;
		return $this;
	}

	public function withPassword($password)
	{
		$this->password = $password;
		return $this;
	}

	public function withSecret($secret)
	{
		$this->secret = $secret;
		return $this;
	}

	public function withAmountOfFailures($amount)
	{
		$this->amountFailedAttempts = $amount;
		return $this;
	}

	public function withExpiration(\DateTimeImmutable $expiration)
	{
		$this->expiration = $expiration;
		return $this;
	}

	public function build(): ReadSlot
	{
		return new ReadSlot($this->writeGuid, $this->readGuid);
	}
}