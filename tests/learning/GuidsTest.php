<?php

namespace App\Tests\learning;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GuidsTest extends TestCase
{
	/**
	 * @test
	 */
	public function can_generate_guids()
	{
		$uuid1 = Uuid::uuid4()->toString();
		$uuid2 = Uuid::uuid4()->toString();

		$this->assertTrue($uuid1 !== $uuid2, 'GUIDS are random always');
	}
}