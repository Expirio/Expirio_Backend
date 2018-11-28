<?php
namespace App\Tests\src\Application\Service;

use App\Application\Service\CommandHandler;
use App\Application\Service\CreatePairSlotsCommand;
use App\Application\Service\PairSlot;
use App\Application\Service\ReadSecretCommand;
use App\Application\Service\WriteSecretCommand;
use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;
use App\Infrastructure\SlotsManagerRedis;
use PHPUnit\Framework\TestCase;
use Predis\Client;

/**
 * @group application
 */
class CommandHandlerTest extends TestCase
{
	private $manager;
	private $redis;

	/** @var CommandHandler */
	private $handler;

	public function setUp()
	{
		$this->redis = new Client([
			"host" => "localhost",
			"port" => 6379
		]);

		$this->manager = new SlotsManagerRedis($this->redis);
		$this->handler = (new CommandHandler($this->manager));
	}

	/**
	 * @test
	 */
	public function create_pair()
	{
		$command = new CreatePairSlotsCommand('writeuid', 'readuid', 'sesame1234');

		$pair = $this->handler->handle($command);

		$this->assertInstanceOf(PairSlot::class, $pair);
	}

	/**
	 * @test
	 */
	public function can_write_secret()
	{
		$createPairCommand = new CreatePairSlotsCommand('writeuid', 'readuid', 'sesame1234');
		$setSecretCommand = new WriteSecretCommand('writeuid', 'this is my secret');

		$this->handler->handle($createPairCommand);
		$this->handler->handle($setSecretCommand);

		$write = $this->manager->fetchSlot('writeuid');
		$read = $this->manager->fetchSlot('readuid');

		$this->assertNull($write);
		$this->assertTrue($read->getPassword() !== 'sesame1234');
		$this->assertInstanceOf(ReadSlot::class, $read);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage The write-slot doesnt exist or is not a write-slot
	 */
	public function cannot_execute_a_write_in_a_read_slot()
	{
		$createPairCommand = new CreatePairSlotsCommand('writeuid', 'readuid', 'sesame1234');
		$setSecretCommand = new WriteSecretCommand('readuid', 'this is my secret');

		$this->handler->handle($createPairCommand);
		$this->handler->handle($setSecretCommand);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage The write-slot doesnt exist or is not a write-slot
	 */
	public function testWhenSlotDoesntExist()
	{
		$createPairCommand = new CreatePairSlotsCommand('writeuid', 'readuid', 'sesame1234');
		$setSecretCommand = new WriteSecretCommand('non_existing_slot_uid', 'this is my secret');

		$this->handler->handle($createPairCommand);
		$this->handler->handle($setSecretCommand);
	}

	/**
	 *
	 * Read secret
	 *
	 */
//	public function testReadSecret()
//	{
//		$command = new ReadSecretCommand('readuid', 'sesame1234');
//
//		$stubManager = $this->createConfiguredMock(SlotsManagerRedis::class, [
//			'fetchSlot' => new ReadSlot('writeuid', 'readUid')
//		]);
//
//		$secret = (new CommandHandler($stubManager))->handle($command);
//
//	}
}