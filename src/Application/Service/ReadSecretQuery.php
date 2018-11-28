<?php

namespace App\Application\Service;

class ReadSecretQuery
{
	/** @var String */
	private $writeUid;

	/** @var String */
	private $password;

	public function __construct(String $writeUid, String $password)
	{
		$this->writeUid = $writeUid;
		$this->password = $password;
	}

	public function getReadUid(): String
	{
		return $this->writeUid;
	}

	public function getPassword(): String
	{
		return $this->password;
	}
}