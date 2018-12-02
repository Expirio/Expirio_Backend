<?php
namespace App\Tests\src\Infrastructure;

use App\Application\CreatePairSlotsCommand;
use App\Domain\ReadSlot\ReadSlot;
use App\Infrastructure\SlotsManagerRedis;
use App\Tests\src\domain\builders\ReadSlotBuilder;
use App\Tests\src\domain\builders\WriteSlotBuilder;
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
	public function returned_null_when_guid_doesnt_exist()
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
		$write = WriteSlotBuilder::any()
			->withReadGuid($this->readuid)
			->withWriteGuid($this->writeuid)
			->build();

		$writeslot = $this->manager->persistSlot($write)->fetchSlot($this->writeuid);

		$this->assertEquals($write, $writeslot);
	}

	// Read slot

	/**
	 * @test
	 */
	public function can_persist_read_slot()
	{
		$read = ReadSlotBuilder::anyWithNoSecret()
			->withGuid($this->readuid)
			->withPassword('sesamo1234')
			->build();

		$readslot = $this->manager->persistSlot($read)->fetchSlot($this->readuid);

		$this->assertEquals($read, $readslot);
	}

	/**
	 * @test
	 */
	public function can_write_and_read_a_read_slot_with_secret()
	{
		$read = ReadSlotBuilder::anyWithNoSecret()
			->withGuid($this->readuid)
			->withPassword('sesamo1234')
			->build()
			->setSecret('this is a secret');

		$readslot = $this->manager->persistSlot($read)->fetchSlot($this->readuid);

		$this->assertInstanceOf(ReadSlot::class, $readslot);
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