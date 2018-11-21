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

	/** @var SlotsManagerRedis */
	private $manager;

	public function setUp()
	{
		$this->redis = new Client([
			"host" => "localhost",
			"port" => 6379
		]);

		$this->manager = new SlotsManagerRedis($this->redis);
	}

	/**
	 * @test
	 */
	public function can_create_read_and_write()
	{
		$writeUid = Uuid::uuid4()->toString();
		$readUid = Uuid::uuid4()->toString();

		$this->manager->createPairSlots($writeUid, $readUid, 'user-password');

		$this->assertSame(['password' => 'user-password'], $this->redis->hgetAll($readUid));
		$this->assertSame(['read_slot' => $readUid], $this->redis->hgetAll($writeUid));
	}

	/**
	 * @test
	 */
	public function can_write_secret()
	{
		$writeUid = Uuid::uuid4()->toString();
		$readUid = Uuid::uuid4()->toString();

		$this->manager->createPairSlots($writeUid, $readUid, 'user-password');
		$this->manager->writeSecret($writeUid, "this text should be secret");

		$this->assertSame(
			0,
			$this->redis->exists($writeUid),
			'The write slot has dissapeared'
		);

		$this->assertSame(
			['secret' => "this text should be secret"],
			$this->redis->hgetAll($readUid),
			'The read slot only contain the secret (encoded), and the pasword is also deleted'
		);
	}

	public function tearDown()
	{
		$this->redis->flushall();
		$this->redis->flushdb();
	}

}