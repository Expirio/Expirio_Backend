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
		$slotRedis = $this->redis->hgetall($guid);

		if (isset($slotRedis['password'])) {
			$secret = $slotRedis['secret'] ?? null;
			return new ReadSlot($guid, $slotRedis['password'], $secret);
		}

		if (isset($slotRedis['read_slot'])) {
			return new WriteSlot($guid, $slotRedis['read_slot']);
		}

		return null;
	}

	public function persistSlot($slot)
	{
		if ($slot instanceof WriteSlot) {
			if ($slot->getSecret() !== null) {
				$clearSecret = $slot->getSecret();
				$this->deleteSlot($slot);
				$readSlot = $this->fetchSlot($slot->getReadUi())->setSecret($clearSecret);
				$this->persistSlot($readSlot);
			} else {
				$this->redis->hmset($slot->getGuid(), ['read_slot' => $slot->getReadUi()]);
			}
		}

		if ($slot instanceof ReadSlot) {
			$data = ['password' => $slot->getPassword()];

			$events = $slot->getEvents();
			if (count($events) > 0 && $events[0] instanceof SecretWasWrittenInReadSlot) {
				$data['secret'] = $slot->getSecret();
			}

			$this->redis->hmset($slot->getGuid(), $data);
		}
	}
}