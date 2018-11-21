<?php

namespace App\Infrastructure;

use App\Application\CreatePairSlotsCommand;
use Predis\Client;

class SlotsManagerRedis
{
	private $redis;

	public function __construct(Client $redis)
	{
		$this->redis = $redis;
	}

	public function createPairSlots(CreatePairSlotsCommand $command)
	{
		// create read slot
		$this->redis->hmset(
			$command->getReadUid(),
			['password' => $command->getReadPassword()]
		);

		// create write slot
		$this->redis->hmset(
			$command->getWriteUid(),
			['read_slot' => $command->getReadUid()]
		);
	}
}