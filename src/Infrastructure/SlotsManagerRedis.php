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

		if (isset($persistenceData['password'])) {
			$secret = !empty($persistenceData['secret']) ? $persistenceData['secret'] :  null;
			return new ReadSlot($guid, $persistenceData['password'], $secret);
		}

		if (isset($persistenceData['read_slot'])) {
			$secret = !empty($persistenceData['secret']) ? $persistenceData['secret'] :  null;
			return new WriteSlot($guid, $persistenceData['read_slot'], $secret);
		}

		return null;
	}

	public function persistSlot($slot)
	{
		if ($slot instanceof WriteSlot) {
			$this->redis->hmset(
				$slot->getGuid(),
				['read_slot' => $slot->getReadUi()]
			);
		}

		if ($slot instanceof ReadSlot) {
			$data = [
				'password' => $slot->getPassword(),
				'secret' => $slot->getSecret()
			];

			$this->redis->hmset($slot->getGuid(), $data);
		}

		return $this;
	}
}