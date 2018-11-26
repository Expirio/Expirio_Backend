<?php

namespace App\Application;

class CreatePairSlotsCommand
{
	private $writeUid;
	private $readUid;
	private $readPassword;


	public function __construct($writeUid, $readUid, $readPassword)
	{
		$this->writeUid = $writeUid;
		$this->readUid = $readUid;
		$this->readPassword = $readPassword;
	}

	public function getWriteUid()
	{
		return $this->writeUid;
	}


	public function getReadUid()
	{
		return $this->readUid;
	}


	public function getReadPassword()
	{
		return $this->readPassword;
	}
}