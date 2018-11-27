<?php
namespace App\Domain\ReadSlot;


class ReadSlot
{
	private $guid;
	private $secret;
	private $password;

	private $events = [];

	public function __construct($guid, $password, $secret = null, $expiration = null)
	{
		$this->guid = $guid;
		$this->password = $password;
		$this->secret = $secret;
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

	public function getEncryptedSecret(): String
	{
		return $this->secret;
	}

	public function getSecret(String $clearPassword)
	{
		if (null == $this->secret) {
			$this->events[] = new AttemptedReadUnexistingSecret($this->guid);

			return null;
		}

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