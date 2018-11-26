<?php

namespace App\Tests\src\Infrastructure;

use App\Application\CreatePairSlotsCommand;
use App\Infrastructure\SlotsManagerRedis;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Ramsey\Uuid\Uuid;

class SlotsManagerRedisTest extends TestCase
{
	/** @var Client */
	private $redis;

	/** @var SlotsManagerRedis */
	private $manager;

	/** @var String */
	private $readuid;

	/** @var String */	
	private $writeuidl;

	public function setUp()
	{
		$this->readuid = Uuid::uuid4()->toString();
		$this->writeuid = Uuid::uuid4()->toString();

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
		$this->manager->createPairSlots($this->writeuid, $this->readuid, 'user-password');

		$this->assertSame(['password' => 'user-password'], $this->redis->hgetAll($this->readuid));
		$this->assertSame(['read_slot' => $this->readuid], $this->redis->hgetAll($this->writeuid));
	}	

	/**
	 * @test
	 */
	public function can_write_secret()
	{
		$this->manager->createPairSlots($this->writeuid, $this->readuid, 'user-password');
		$this->manager->writeSecret($this->writeuid, "this text should be secret");

		$this->assertSame(
			0,
			$this->redis->exists($this->writeuid),
			'The write slot has dissapeared'
		);

		$this->assertSame(
			['secret' => "this text should be secret"],
			$this->redis->hgetAll($this->readuid),
			'The read slot only contain the secret (encoded), and the pasword is also deleted'
		);
	}

	/**
	 * @test
	 */
	public function can_read_secret()
	{
		$this->markTestSkipped('not implemented yet');
	}


	public function tearDown()
	{
		$this->redis->flushall();
		$this->redis->flushdb();
	}
}