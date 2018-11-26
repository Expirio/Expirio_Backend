<?php
namespace App\Domain;


class ReadSlot
{
	private $guid;
	private $secret;
	private $password;

	private $events = [];

	private function __construct($guid, $pass = null)
	{
		$this->guid = $guid;
		$this->password = $pass;
	}

	public static function withPassword(String $guid, String $pass)
	{
		return new self($guid, $pass, null);
	}

	public function getSecret(String $clearPassword)
	{
		if (sha1($clearPassword) === $this->password) {
			$this->events[] = new SecretWasRead($this->guid);

			return $this->decrypt($this->secret, $clearPassword);
		}

		$this->events[] = new UsedWrongPasswordWhenReading($this->guid);
		return null;
	}

	public function setSecret(String $secret)
	{
		$this->secret = $this->encrypt($secret, $this->password);
		$this->password = $this->hash($this->password);

		$this->events[] = new SecretWasWrittenInReadSlot($this->guid);

		return $this;
	}

	public function getEvents()
	{
		return $this->events;
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
}