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
	public function can_create_read_and_write()
	{
		$read = new ReadSlot($this->readuid, 'sesamo1234');
		$write = new WriteSlot($this->writeuid, $this->readuid);

		$this->manager->persistSlot($write);
		$this->manager->persistSlot($read);

		$readslot = $this->manager->fetchSlot($this->readuid);
		$writeslot = $this->manager->fetchSlot($this->writeuid);

		$this->assertInstanceOf(ReadSlot::class, $readslot);
		$this->assertInstanceOf(WriteSlot::class, $writeslot);
	}

	/**
	 * @test
	 */
	public function when_write_secret_then_write_slot_is_deleted()
	{
		$read = new ReadSlot($this->readuid, 'sesamo1234');
		$write = new WriteSlot($this->writeuid, $this->readuid);
		$this->manager->persistSlot($write);
		$this->manager->persistSlot($read);

		$writeSlot = $this->manager
			->fetchSlot($this->writeuid)
			->setSecret('this is a secret');

		$this->manager->persistSlot($writeSlot);

		$write = $this->manager->fetchSlot($this->writeuid);
		$this->assertNull($write, 'write slot dissapear once that is written');
	}

	/**
	 * @test
	 */
	public function when_write_secret_then_read_is_updated()
	{
		$read = new ReadSlot($this->readuid, 'sesamo1234');
		$write = new WriteSlot($this->writeuid, $this->readuid);
		$this->manager->persistSlot($write);
		$this->manager->persistSlot($read);

		$read = $this->manager->fetchSlot($this->readuid);
		$this->assertEquals('sesamo1234', $read->getPassword(), 'There is no secret yet, so the password is in clear text for now, there is no secret');

		$writeSlot = $this->manager
			->fetchSlot($this->writeuid)
			->setSecret('this is a secret');
		$this->manager->persistSlot($writeSlot);

		$read = $this->manager->fetchSlot($this->readuid);
		$this->assertTrue($read->getPassword() !== 'sesamo1234', 'Now there is a secret, so the password shouldnt be clear');
		$this->assertTrue($read->getEncryptedSecret() !== 'this is a secret', 'The secret must be encrypted');
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