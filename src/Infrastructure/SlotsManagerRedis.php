<?php

namespace App\Infrastructure;

use Predis\Client;

class SlotsManagerRedis
{
	private $redis;

	public function __construct(Client $redis)
	{
		$this->redis = $redis;
	}

	public function createPairSlots(string $writeUid, string $readUid, string $password)
	{
		// create read slot
		$this->redis->hmset(
			$readUid,
			['password' => $password]
		);

		// create write slot
		$this->redis->hmset(
			$writeUid,
			['read_slot' => $readUid]
		);
	}

	public function writeSecret($writeUid, $secretText)
	{
		$readUid = $this->redis->hget($writeUid, 'read_slot');
		$this->redis->del($writeUid);

		$this->redis->hdel($readUid, ['password']);
		$this->redis->hset($readUid, 'secret', $secretText);
	}
}