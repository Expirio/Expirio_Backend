<?php

namespace App\Domain\ReadSlot;

class ReadSlotWasExpired
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