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

	public static function any() {
		$faker = Factory::create();

		$self = new self();
		return $self
			->withGuid(Uuid::uuid4()->toString())
			->withPassword($faker->word)
			->withSecret($faker->sentence)
			->withAmountOfFailures($faker->numberBetween(0, 2));
	}

	public static function anyWithNoSecret() {
		$faker = Factory::create();

		$self = new self();
		return $self
			->withGuid(Uuid::uuid4()->toString())
			->withPassword($faker->word)
			->withAmountOfFailures($faker->numberBetween(0, 2));
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

	public function build(): ReadSlot
	{
		return new ReadSlot(
			$this->guid,
			$this->password,
			$this->secret,
			$this->amountFailedAttempts
		);
	}
}