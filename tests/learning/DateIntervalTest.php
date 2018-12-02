<?php

namespace App\Tests\learning;

use DateInterval;
use PHPUnit\Framework\TestCase;

/**
 * @group learning
 */
class DateIntervalTest extends TestCase
{
	/**
	 * @test
	 */
	public function when_interval_is_specified_in_seconds()
	{
		$dateIntervalInSeconds = new DateInterval('PT2S');
		$this->assertEquals(
			2,
			$dateIntervalInSeconds->s,
			'PT2S must be the same than 2 seconds'
		);
	}

	/**
	 * @test
	 */
	public function when_interval_is_specified_in_hours()
	{
		$dateIntervalInHours = new DateInterval('PT3H');

		$this->assertEquals(0, $dateIntervalInHours->s, 'PT3H must be 0 seconds');
		$this->assertEquals(3, $dateIntervalInHours->h, 'PT3H must be the same than 3 hours');
		$this->assertEquals(0, $dateIntervalInHours->format('%s'));
	}
}