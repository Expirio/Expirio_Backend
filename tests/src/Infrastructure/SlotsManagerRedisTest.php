<?php

namespace App\Tests\src\Infrastructure;

use App\Application\CreatePairSlotsCommand;
use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;
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
	private $writeuid;

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
		$read = new ReadSlot($this->readuid, 'sesamo1234');
		$write = new WriteSlot($this->writeuid, $this->readuid);

		$this->manager->createPairSlots($write, $read);

		$readslot = $this->manager->fetchSlot($this->readuid);
		$writeslot = $this->manager->fetchSlot($this->writeuid);

		$this->assertInstanceOf(ReadSlot::class, $readslot);
		$this->assertInstanceOf(WriteSlot::class, $writeslot);
	}

	/**
	 * @test
	 */
	public function write_slot_is_of_one_use()
	{
		$read = new ReadSlot($this->readuid, 'sesamo1234');
		$write = new WriteSlot($this->writeuid, $this->readuid);
		$this->manager->createPairSlots($write, $read);

		$writeSlot = $this->manager
			->fetchSlot($this->writeuid)
			->setSecret('this is a secret');

		$this->manager->persistSecret($writeSlot);

		$this->assertNull($this->manager->fetchSlot($this->writeuid), 'write slot dissapear once that is written');
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