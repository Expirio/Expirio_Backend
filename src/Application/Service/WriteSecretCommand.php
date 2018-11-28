<?php

namespace App\Application\Service;

class WriteSecretCommand
{
	private $writeUid;
	private $secret;

	public function __construct(String $writeUid, String $secret)
	{
		$this->writeUid = $writeUid;
		$this->secret = $secret;
	}

	public function getWriteUid(): String
	{
		return $this->writeUid;
	}

	public function getSecret(): String
	{
		return $this->secret;
	}
}