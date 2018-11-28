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
			$secret = null;
			if (!empty($persistenceData['secret'])) {
				$secret = $persistenceData['secret'];
			}

			return new ReadSlot($guid, $persistenceData['password'], $secret);
		}

		if (isset($persistenceData['read_slot'])) {
			$write = new WriteSlot($guid, $persistenceData['read_slot']);
			if (!empty($persistenceData['secret'])) {
				$write->setSecret($persistenceData['secret']);
			}

			return $write;
		}

		return null;
	}

	public function persistSlot($slot)
	{
		if ($slot instanceof WriteSlot) {
			// IMPORTANT: I TRIED TO DELETE FROM REDIS THIS WRITE SLOT AND UPDATE (PERSISTINT AS WELL) THE READ ONE. THIS APPROACH IS REALLY WRONG
			// THE REASON IS THAT IT STORES TWO AGGREGATES IN ONLY ONE TRANSACTION, WHICH CREATE PROBLEMS AND HARDER CODE TO MAINTAIN. (ALSO TESTS
			// WERE MUCH MORE DIFFICULT)
			$this->redis->hmset($slot->getGuid(), [
				'read_slot' => $slot->getReadUi(),
				'secret' => $slot->getSecret()
			]);
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