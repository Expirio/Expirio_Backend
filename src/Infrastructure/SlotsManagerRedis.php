<?php

namespace App\Infrastructure;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;
use Predis\Client;

class SlotsManagerRedis
{
	private $redis;

	public function __construct(Client $redis)
	{
		$this->redis = $redis;
	}

	public function createPairSlots(WriteSlot $write, ReadSlot $read)
	{
		// create write slot
		$this->redis->hmset(
			$write->getGuid(),
			['read_slot' => $read->getGuid()]
		);

		// create read slot
		$this->redis->hmset(
			$read->getGuid(),
			['password' => $read->getPassword()]
		);
	}

	public function persistSecret($writeSlot)
	{
		$this->deleteSlot($writeSlot);

		$readSlot = $this->fetchSlot($writeSlot->getReadUi());
		$readSlot->setSecret($writeSlot->getSecret());
	}

	public function deleteSlot($slot)
	{
		$this->redis->del($slot->getGuid());
	}

	public function fetchSlot(String $guid)
	{
		$slotRedis = $this->redis->hgetall($guid);

		if (isset($slotRedis['password'])) {
			return new ReadSlot($guid, $slotRedis['password']);
		}

		if (isset($slotRedis['read_slot'])) {
			return new WriteSlot($guid, $slotRedis['read_slot']);
		}

		return null;
	}
}