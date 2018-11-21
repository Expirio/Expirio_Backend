<?php

namespace App\Tests\learning;

use PHPUnit\Framework\TestCase;

class HasherTest extends TestCase
{
	/**
	 * @test
	 */
	function hash_multiple_times()
	{
		$text = "good morning";

		$hash1 = sha1($text);
		$hash2 = sha1($text);

		$this->assertEquals($hash1, $hash2, 'sha1 produces the same hash always for the same text');
		$this->assertEquals('a8cbec7254dd9499e2f86d55098931a41db62d6e', $hash1);
	}
}