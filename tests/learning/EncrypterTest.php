<?php
namespace App\Tests\learning;

use PHPUnit\Framework\TestCase;

class EncrypterTest extends TestCase
{
	/**
	 * @test
	 */
	public function can_encrypt_and_decrypt()
	{
		$secretText = 'hello my name is Francisco';

		$encrypted = $this->encrypt($secretText, 'sesamo1234');
		$decrypted = $this->decrypt($encrypted, 'sesamo1234');

		$this->assertEquals($secretText, $decrypted);
	}

	private function encrypt($string_to_encrypt, $password) {
		return openssl_encrypt($string_to_encrypt,"AES-128-ECB",$password);
	}

	private function decrypt($encrypted_string, $password) {
		return openssl_decrypt($encrypted_string,"AES-128-ECB",$password);
	}
}