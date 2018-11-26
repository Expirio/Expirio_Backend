<?php

namespace App\Domain\WriteSlot;

class WriteSlot
{
	private $guid;
	private $readGuid;
	private $secret;

	private $events = [];

	public function __construct(String $guid, String $readGuid, $expiration = null)
	{
		$this->guid = $guid;
		$this->readGuid = $readGuid;
	}

	public function setSecret(String $secret)
	{
		$this->secret = $secret;
		$this->events[] = new WriteSlotWasWritten($this->guid);

		return $this;
	}

	public function getReadUi()
	{
		return $this->readGuid;
	}

	public function getSecret()
	{
		return $this->secret;
	}

	public function getGuid(): String
	{
		return $this->guid;
	}

	public function getEvents()
	{
		return $this->events;
	}
}