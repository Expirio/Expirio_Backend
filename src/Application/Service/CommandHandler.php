<?php

namespace App\Application\Service;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;
use App\Infrastructure\SlotsManagerRedis;
use ReflectionClass;

class CommandHandler
{
	/** @var SlotsManagerRedis */
	private $redisManager;

	public function __construct(SlotsManagerRedis $redisManager)
	{
		$this->redisManager = $redisManager;
	}

	public function handle($command)
	{
		$handleMethod = 'handle' . (new ReflectionClass($command))->getShortName();
		return $this->$handleMethod($command);
	}

	private function handleCreatePairSlotsCommand(CreatePairSlotsCommand $command): PairSlot
	{
		$read = new ReadSlot($command->getReadUid(), $command->getReadPassword());
		$write = new WriteSlot($command->getWriteUid(), $command->getReadUid());

		$this->redisManager->persistSlot($write);
		$this->redisManager->persistSlot($read);

		return new PairSlot($read, $write);
	}

	private function handleWriteSecretCommand(WriteSecretCommand $command)
	{
		$writeSlot = $this->redisManager->fetchSlot($command->getWriteUid());

		if ($writeSlot && $writeSlot instanceof WriteSlot) {
			$writeSlot->setSecret($command->getSecret());

			$this->redisManager->persistSlot($writeSlot);
			return true;
		}

		return false;
	}

	private function handleReadSecretCommand(ReadSecretCommand $command)
	{
		$readSlot = $this->redisManager->fetchSlot($command->getReadUid());

		if ($readSlot) {
			$secret = $readSlot->revealSecret($command->getPassword());
			if ($secret) {
				return $secret;
			}
		}

		return null;
	}
}