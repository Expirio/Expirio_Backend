<?php

namespace App\Tests\src\Infrastructure;

use App\Application\CreatePairSlotsCommand;
use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;
use App\Infrastructure\SlotsManagerRedis;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Ramsey\Uuid\Uuid;

/**
 * @group infrastructure
 */
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

	// Write slot

	/**
	 * @test
	 */
	public function can_write_and_read_a_write_slot()
	{
		$write = new WriteSlot($this->writeuid, $this->readuid);

		$this->manager->persistSlot($write);

		$writeslot = $this->manager->fetchSlot($this->writeuid);

		$this->assertInstanceOf(WriteSlot::class, $writeslot);
		$this->assertNull($writeslot->getSecret());
		$this->assertEquals($this->writeuid, $writeslot->getGuid());
		$this->assertEquals($this->readuid, $writeslot->getReadUi());
	}

	/**
	 * @test
	 */
	public function can_write_and_read_a_write_slot_with_secret()
	{
		$write = new WriteSlot($this->writeuid, $this->readuid, 'this is my secret');

		$this->manager->persistSlot($write);

		$writeslot = $this->manager->fetchSlot($this->writeuid);

		$this->assertEquals('this is my secret', $writeslot->getSecret());
		$this->assertEquals($this->writeuid, $writeslot->getGuid());
		$this->assertEquals($this->readuid, $writeslot->getReadUi());
	}

	// Read slot

	/**
	 * @test
	 */
	public function can_write_and_read_a_read_slot()
	{
		$read = new ReadSlot($this->readuid, 'sesamo1234');

		$this->manager->persistSlot($read);

		$readslot = $this->manager->fetchSlot($this->readuid);

		$this->assertInstanceOf(ReadSlot::class, $readslot);
		$this->assertNull($readslot->getSecret());
		$this->assertEquals($this->readuid, $readslot->getGuid());
		$this->assertEquals('sesamo1234', $readslot->getPassword());
	}


	/**
	 * @test
	 */
	public function can_write_and_read_a_read_slot_with_secret()
	{
		$read = new ReadSlot($this->readuid, 'sesamo1234', 'this is a secret');

		$this->manager->persistSlot($read);

		$readslot = $this->manager->fetchSlot($this->readuid);

		$this->assertInstanceOf(ReadSlot::class, $readslot);
		$this->assertEquals('this is a secret', $readslot->getSecret());
		$this->assertEquals($this->readuid, $readslot->getGuid());
		$this->assertEquals('sesamo1234', $readslot->getPassword());
	}

	public function tearDown()
	{
		$this->redis->flushall();
		$this->redis->flushdb();
	}
}