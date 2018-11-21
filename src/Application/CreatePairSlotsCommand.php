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

	/**
	 * @return mixed
	 */
	public function getWriteUid()
	{
		return $this->writeUid;
	}

	/**
	 * @return mixed
	 */
	public function getReadUid()
	{
		return $this->readUid;
	}

	/**
	 * @return mixed
	 */
	public function getReadPassword()
	{
		return $this->readPassword;
	}
}