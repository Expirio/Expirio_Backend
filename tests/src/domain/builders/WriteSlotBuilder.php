<?php

namespace App\Tests\src\domain\builders;

use App\Domain\WriteSlot\WriteSlot;
use Ramsey\Uuid\Uuid;

class WriteSlotBuilder
{
	private $writeGuid;
	private $readGuid;

	public function any() {
		$this->readGuid = Uuid::uuid4()->toString();
		$this->readGuid = Uuid::uuid4()->toString();
	}

	public function withReadGuid($readGuid)
	{
		$this->readGuid = $readGuid;
		return $this;
	}

	public function withWriteGuid($writeGuid)
	{
		$this->writeGuid = $writeGuid;
		return $this;
	}

	public function build(): WriteSlot
	{
		return new WriteSlot($this->writeGuid, $this->readGuid);
	}
}