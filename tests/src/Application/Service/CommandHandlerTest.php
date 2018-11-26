<?php

namespace App\Tests\src\Application\Service;

use App\Application\Service\CommandHandler;
use App\Application\Service\CreatePairSlotsCommand;
use App\Application\Service\WriteSecretCommand;
use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;
use App\Infrastructure\SlotsManagerRedis;
use PHPUnit\Framework\TestCase;

class CommandHandlerTest extends TestCase
{
	/**
	 *
	 * Create pair
	 *
	 */
	public function testCreatePair()
	{
		$command = new CreatePairSlotsCommand('writeuid', 'readuid', 'sesame1234');

		$mockRedisManager = $this->createMock(SlotsManagerRedis::class);
		$mockRedisManager
			->expects($this->once())
			->method('createPairSlots');

		(new CommandHandler($mockRedisManager))->handle($command);
	}

	/**
	 *
	 * write secret
	 *
	 */
	public function testWriteSecretCheckFirstlyTypeOfSlot()
	{
		$command = new WriteSecretCommand('writeuid', 'this is my secret');

		$stubManager = $this->createConfiguredMock(SlotsManagerRedis::class, [
			'fetchSlot' => new WriteSlot('writeuid', 'readUid')
		]);

		$result = (new CommandHandler($stubManager))->handle($command);
		$this->assertTrue($result);
	}

	public function testWriteSecretCheckFirstlyTypeOfSlotForWrongType()
	{
		$command = new WriteSecretCommand('writeuid', 'this is my secret');

		$stubManager = $this->createConfiguredMock(SlotsManagerRedis::class, [
			'fetchSlot' => new ReadSlot('writeuid', 'readUid')
		]);

		$result = (new CommandHandler($stubManager))->handle($command);
		$this->assertFalse($result, 'a secret cannot be written in a write read slot');
	}

	/**
	 *
	 * Read secret
	 *
	 */
}