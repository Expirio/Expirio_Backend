<?php

namespace App\Domain\ReadSlot;

class SecretWasWrittenInReadSlot
{
	private $guid;

	public function __construct(String $readGuid)
	{
		$this->guid = $readGuid;
	}

	public function getGuid()
	{
		return $this->guid;
	}
}