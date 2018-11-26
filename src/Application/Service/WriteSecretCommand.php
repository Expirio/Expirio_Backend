<?php

namespace App\Application\Service;

class WriteSecretCommand
{
	private $writeUid;
	private $secret;

	public function __construct($writeUid, $secret)
	{
		$this->writeUid = $writeUid;
		$this->secret = $secret;
	}

	public function getWriteUid()
	{
		return $this->writeUid;
	}

	public function getSecret()
	{
		return $this->secret;
	}
}