<?php

namespace App\Tests\src\Infrastructure;

use App\Application\CreatePairSlotsCommand;
use App\Infrastructure\SlotsManagerRedis;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Ramsey\Uuid\Uuid;

class SlotsManagerRedisTest extends TestCase
{
	private $redis;

	public function setUp()
	{
		$this->redis = new Client([
			"host" => "localhost",
			"port" => 6379
		]);
	}

	/**
	 * @test
	 */
	public function can_create_read_and_write()
	{
		$writeUid = Uuid::uuid4()->toString();
		$readUid = Uuid::uuid4()->toString();

		(new SlotsManagerRedis($this->redis))->createPairSlots(
			new CreatePairSlotsCommand($writeUid, $readUid, 'user-password')
		);

		$this->assertSame(['password' => 'user-password'], $this->redis->hgetAll($readUid));
		$this->assertSame(['read_slot' => $readUid], $this->redis->hgetAll($writeUid));


	}


}