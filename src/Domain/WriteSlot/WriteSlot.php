<?php

namespace App\Domain\WriteSlot;

class WriteSlot
{
	private $guid;
	private $readGuid;
	private $secret;

	private $events = [];

	public function __construct(String $guid, String $readGuid, $secret = null, $expiration = null)
	{
		$this->guid = $guid;
		$this->readGuid = $readGuid;
		$this->secret = $secret;
	}

	public function setSecret(String $secret)
	{
		if (!is_null($this->secret)) {
			throw new \Exception('The secret was already set in the write slot and cannot be replaced');
		}

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