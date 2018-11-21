<?php

namespace App\Tests\learning;

use PHPUnit\Framework\TestCase;
use Predis\Client;

class RedisTest extends TestCase
{
	/** @var Client */
	private $redis;

	public function setUp()
	{
		$this->redis = new Client([
			"host" => "localhost",
			"port" => 6379
		]);
	}

	/**
	 * @test
	 */
	public function redis_is_live()
	{
		$response = $this->redis->ping();
		$this->assertEquals('PONG', $response->getPayload(), "Install and run redis-server");
	}

	/**
	 * @test
	 */
	public function can_persist_and_read_whole_hash()
	{
		$hash = ['country' => 'Africa', 'age' => 12];

		$this->redis->hmset('person', $hash);

		$this->assertSame(
			$hash = ['country' => 'Africa', 'age' => '12'],
			$this->redis->hgetall('person'),
			'Numbers are not converted to int when reading'
		);
	}


}