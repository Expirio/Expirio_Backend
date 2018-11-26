<?php

namespace App\Domain;

class SecretWasRead
{
	private $guid;

	public function __construct(String $guid)
	{
		$this->guid = $guid;
	}

	public function getGuid()
	{
		return $this->guid;
	}
}