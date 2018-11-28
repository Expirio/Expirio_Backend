<?php
namespace App\Domain\ReadSlot;


use Exception;

class ReadSlot
{
	private $guid;
	private $password;
	private $secret;
	private $amountFailedAttempts;
	private $events = [];

	public function __construct($guid, $password, $secret = null, Int $amountFailedAttempts = 0, $expiration = null)
	{
		$this->guid = $guid;
		$this->password = $password;
		$this->secret = $secret;
		$this->amountFailedAttempts = $amountFailedAttempts;
	}

	public function getEvents()
	{
		return $this->events;
	}

	public function getGuid(): String
	{
		return $this->guid;
	}

	public function getPassword(): String
	{
		return $this->password;
	}

	public function getSecret(): ?String
	{
		return $this->secret;
	}

	public function revealSecret(String $clearPassword): ?String
	{
		if (null == $this->secret) {
			return null;
		}

		if (sha1($clearPassword) !== $this->password) {
			$this->amountFailedAttempts++;

			if($this->amountFailedAttempts >=3) {
				throw new Exception('Maximum attempts reached with wrong password');
			}

			throw new Exception('Invalid password');
		}

		return $this->decrypt($this->secret, $clearPassword);
	}

	public function setSecret(String $secret)
	{
		if (!is_null($this->secret)) {
			throw new Exception('Secret cannot be set more than once. This is a violation');
		}

		$this->secret = $this->encrypt($secret, $this->password);
		$this->password = $this->hash($this->password);

		$this->events[] = new SecretWasWrittenInReadSlot($this->guid);

		return $this;
	}



	private function encrypt($clearSecret, $password) {
		return openssl_encrypt($clearSecret,"AES-128-ECB",$password);
	}

	private function decrypt($encryptedSecret, $password) {
		return openssl_decrypt($encryptedSecret,"AES-128-ECB",$password);
	}

	private function hash(string $text)
	{
		return sha1($text);
	}

	private function getAmountOfAttempts(): Int
	{
		$attemps = 0;
		$events = $this->getEvents();

		foreach($events as $event) {
			if($event instanceof UsedWrongPasswordWhenReading) {
				$attemps++;
			}
		}

		return $attemps;
	}
}