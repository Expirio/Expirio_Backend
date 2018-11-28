<?php
namespace App\Tests\src\Application\Service;

use App\Application\Service\CommandHandler;
use App\Application\Service\CreatePairSlotsCommand;
use App\Application\Service\PairSlot;
use App\Application\Service\ReadSecretCommand;
use App\Application\Service\ReadSecretQuery;
use App\Application\Service\WriteSecretCommand;
use App\Domain\ReadSlot\ReadSlot;
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
	 * @test
	 */
	public function can_receive_the_secret_if_used_proper_password()
	{
		$createPairCommand = new CreatePairSlotsCommand('writeuid', 'readuid', 'sesame1234');
		$setSecretCommand = new WriteSecretCommand('writeuid', 'this is my secret');
		$this->handler->handle($createPairCommand);
		$this->handler->handle($setSecretCommand);

		$readQuery = new ReadSecretQuery('readuid', 'sesame1234');
		$secret = $this->handler->handle($readQuery);

		$this->assertEquals('this is my secret', $secret);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage The read slot doesnt exist or the password is invalid
	 */
	public function cannot_read_secret_if_slot_doesnt_exist()
	{
		$readQuery = new ReadSecretQuery('non_existing_uid', 'sesame1234');

		$this->handler->handle($readQuery);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage Invalid password
	 */
	public function cannot_read_secret_if_password_is_wrong()
	{
		$createPairCommand = new CreatePairSlotsCommand('writeuid', 'readuid', 'sesame1234');
		$setSecretCommand = new WriteSecretCommand('writeuid', 'this is my secret');
		$this->handler->handle($createPairCommand);
		$this->handler->handle($setSecretCommand);


		$readQuery = new ReadSecretQuery('readuid', 'wrong_password');
		$this->handler->handle($readQuery);
	}
}