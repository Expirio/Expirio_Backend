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

	/**
	 * @test
	 */
	public function returned_value_when_guid_doesnt_exist()
	{
		$writeslot = $this->manager->fetchSlot('this doesnt exist');
		$this->assertNull($writeslot);
	}

	// Write slot

	/**
	 * @test
	 */
	public function can_persist_write_slot()
	{
		$write = new WriteSlot($this->writeuid, $this->readuid);

		$this->manager->persistSlot($write);

		$writeslot = $this->manager->fetchSlot($this->writeuid);

		$this->assertInstanceOf(WriteSlot::class, $writeslot);
		$this->assertEquals($this->writeuid, $writeslot->getGuid());
		$this->assertEquals($this->readuid, $writeslot->getReadUi());
	}

	// Read slot

	/**
	 * @test
	 */
	public function can_persist_basic_read_slot()
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
	public function can_persist_read_slot_with_some_attempts()
	{
		$read = new ReadSlot($this->readuid, 'sesamo1234', 'any secret', 4);

		$this->manager->persistSlot($read);
		$readslot = $this->manager->fetchSlot($this->readuid);

		$this->assertInstanceOf(ReadSlot::class, $readslot);
		$this->assertEquals(4, $readslot->getAmountOfAttempts());
	}


	/**
	 * @test
	 */
	public function can_write_and_read_a_read_slot_with_secret()
	{
		$read = new ReadSlot($this->readuid, 'sesamo1234');
		$read->setSecret('this is a secret');

		$this->manager->persistSlot($read);

		$readslot = $this->manager->fetchSlot($this->readuid);

		$this->assertInstanceOf(ReadSlot::class, $readslot);
		$this->assertEquals($this->readuid, $readslot->getGuid());
		$this->assertTrue('this is a secret' !== $readslot->getSecret());
		$this->assertTrue('sesamo1234' !== $readslot->getPassword());
		$this->assertTrue('this is a secret' == $readslot->revealSecret('sesamo1234'));
	}

	public function tearDown()
	{
		$this->redis->flushall();
		$this->redis->flushdb();
	}
}