<?php
namespace App\Application\Service;

use DateInterval;

class CreatePairSlotsCommand
{
	private $writeUid;
	private $readUid;
	private $readPassword;
	private $expirationInterval;

	public function __construct($writeUid, $readUid, $readPassword, String $expireInterval)
	{
		$this->writeUid = $writeUid;
		$this->readUid = $readUid;
		$this->readPassword = $readPassword;
		$this->expirationInterval = new DateInterval($expireInterval);
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

	public function getExpirationSeconds(): Int
	{
		$d = $this->expirationInterval;
		return $d->s + ($d->i * 60) + ($d->h * 3600) + ($d->d * 86400) + ($d->m * 2592000);
	}
}