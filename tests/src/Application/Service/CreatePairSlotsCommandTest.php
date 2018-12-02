<?php
namespace App\Tests\src\Application\Service;

use App\Application\Service\CreatePairSlotsCommand;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @group application
 */
class CreatePairSlotsCommandTest extends TestCase
{
	/** @var DateTimeImmutable */
	private $now;

	public function setUp()
	{
		$this->now = new DateTimeImmutable();
	}

	/**
	 * @test
	 */
	public function expiration_specified_in_days()
	{
		$secondsInOneDay = 86400;

		$command = $this->command('P1D');
		$this->assertEquals($secondsInOneDay, $command->getExpirationSeconds());
	}

	/**
	 * @test
	 */
	public function expiration_specified_in_hours()
	{
		$seondsInOneHour = 3600;

		$command = $this->command('PT2H');
		$this->assertEquals($seondsInOneHour*2, $command->getExpirationSeconds());
	}

	/**
	 * @test
	 */
	public function expiration_specified_in_hours_and_minutes()
	{
		$seondsInOneHour = 3600;
		$secondsInOneDay = 86400;

		$command = $this->command('P1DT2H');
		$this->assertEquals($secondsInOneDay + 2*$seondsInOneHour, $command->getExpirationSeconds());
	}

	private function command(String $expirationIn): CreatePairSlotsCommand
	{
		return new CreatePairSlotsCommand('uid1', 'read1', 'password1', $expirationIn);
	}
}