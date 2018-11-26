<?php

namespace App\Domain\ReadSlot;

class AttemptedReadUnexistingSecret
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