<?php

namespace App\Domain\WriteSlot;

class WriteSlot
{
	private $guid;
	private $readGuid;

	public function __construct(String $guid, String $readGuid, $expiration = null)
	{
		$this->guid = $guid;
		$this->readGuid = $readGuid;
	}

	public function getReadUi()
	{
		return $this->readGuid;
	}

	public function getGuid(): String
	{
		return $this->guid;
	}
}