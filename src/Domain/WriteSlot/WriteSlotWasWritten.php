<?php

namespace App\Domain\WriteSlot;

class WriteSlotWasWritten
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