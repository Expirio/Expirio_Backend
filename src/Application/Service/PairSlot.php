<?php

namespace App\Application\Service;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;

class PairSlot
{
	private $read;
	private $write;

	public function __construct(ReadSlot $readSlot, WriteSlot $writeSlot)
	{
		$this->read = $readSlot->getGuid();
		$this->write = $writeSlot->getGuid();
	}

	public function getWrite(): String
	{
		return $this->write;
	}

	public function getRead(): String
	{
		return $this->read;
	}
}