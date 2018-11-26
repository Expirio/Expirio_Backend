<?php

namespace App\Application\Service;

class ReadSecretCommand
{
	private $readUid;
	private $password;

	public function __construct($readUid, $password)
	{
		$this->readUid = $readUid;
		$this->password = $password;
	}

	public function getReadUid()
	{
		return $this->readUid;
	}

	public function getPassword()
	{
		return $this->password;
	}
}