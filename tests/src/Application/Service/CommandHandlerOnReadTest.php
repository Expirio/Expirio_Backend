<?php
namespace App\Tests\src\Application\Service;

use App\Application\Service\CommandHandler;
use App\Application\Service\CreatePairSlotsCommand;
use App\Application\Service\ReadSecretCommand;
use App\Application\Service\ReadSecretQuery;
use App\Application\Service\WriteSecretCommand;
use App\Infrastructure\SlotsManagerRedis;
use App\Tests\src\domain\builders\ReadSlotBuilder;
use PHPUnit\Framework\TestCase;
use Predis\Client;

/**
 * @group application
 */
class CommandHandlerOnReadTest extends TestCase
{
	/** @var SlotsManagerRedis */
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
	public function can_receive_the_secret_if_used_proper_password()
	{
		$this->givenAPair();

		$readQuery = new ReadSecretQuery('readuid', 'sesame1234');
		$secret = $this->handler->handle($readQuery);

		$this->assertEquals('this is my secret', $secret);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage The read-slot doesnt exist or the password to read is invalid
	 */
	public function cannot_read_if_readid_is_incorrect()
	{
		$readQuery = new ReadSecretQuery('WRONG_READ_ID', 'sesame1234');

		$this->handler->handle($readQuery);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage Reading slot: Invalid password
	 */
	public function cannot_read_secret_if_password_is_wrong()
	{
		$this->givenReadSlotWithFailedAttempts(1);

		$readQuery = new ReadSecretQuery('readuid', 'wrong_password');
		$this->handler->handle($readQuery);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 * @expectedExceptionMessage Reading slot: Max failed attempts readched
	 */
	public function read_is_deleted_if_attempted_toomuch()
	{
		$this->givenReadSlotWithFailedAttempts(2);

		$readQuery1 = new ReadSecretQuery('readuid', 'wrong password 1');
		$this->handler->handle($readQuery1);
	}

	private function givenAPair()
	{
		$createPairCommand = new CreatePairSlotsCommand('writeuid', 'readuid', 'sesame1234', 'P1D');
		$setSecretCommand = new WriteSecretCommand('writeuid', 'this is my secret');
		$this->handler->handle($createPairCommand);
		$this->handler->handle($setSecretCommand);
	}

	private function givenReadSlotWithFailedAttempts($amount)
	{
		$readSlot = ReadSlotBuilder::anyWithNoSecret()
			->withGuid('readuid')
			->withPassword('sesamo1234')
			->withAmountOfFailures($amount)
			->withSecret('this is my secret')
			->build();

		$this->manager->persistSlot($readSlot);
	}
}