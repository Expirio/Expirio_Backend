<?php

namespace App\Application\Service;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;

class PairSlot
{
	private $readUid;
	private $writeUid;

	public function __construct(ReadSlot $readSlot, WriteSlot $writeSlot)
	{
		$this->readUid = $readSlot->getGuid();
		$this->writeUid = $writeSlot->getGuid();
	}

	public function getWriteUid(): String
	{
		return $this->writeUid;
	}

	public function getReadUid(): String
	{
		return $this->readUid;
	}
}