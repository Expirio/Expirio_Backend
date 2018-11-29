<?php
namespace App\Tests\src\Application\Service;

use App\Application\Service\CommandHandler;
use App\Application\Service\CreatePairSlotsCommand;
use App\Application\Service\PairSlot;
use App\Application\Service\ReadSecretCommand;
use App\Infrastructure\SlotsManagerRedis;
use PHPUnit\Framework\TestCase;
use Predis\Client;

/**
 * @group application
 */
class CommandHandlerOnCreatePairTest extends TestCase
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
}