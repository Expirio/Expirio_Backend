<?php

namespace App\Infrastructure;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\ReadSlot\SecretWasWrittenInReadSlot;
use App\Domain\WriteSlot\WriteSlot;
use Predis\Client;

class SlotsManagerRedis
{
	private $redis;

	public function __construct(Client $redis)
	{
		$this->redis = $redis;
	}


	public function deleteSlot($slot)
	{
		$this->redis->del($slot->getGuid());
	}

	public function fetchSlot(String $guid)
	{
		$persistenceData = $this->redis->hgetall($guid);

		if ($persistenceData) {
			return Mapper::toDomain($guid, $persistenceData);
		}

		return null;
	}

	public function persistSlot($slot)
	{
		if ($slot instanceof WriteSlot) {
			$this->redis->hmset(
				$slot->getGuid(),
				Mapper::toPersistence($slot)
			);
		}

		if ($slot instanceof ReadSlot) {
			$this->redis->hmset(
				$slot->getGuid(),
				Mapper::toPersistence($slot)
			);
		}

		return $this;
	}
}