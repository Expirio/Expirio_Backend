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
		$hashRead = $this->redis->hgetall('person');

		$this->assertSame(['country' => 'Africa', 'age' => '12'], $hashRead,
			'Numbers are not converted to int when reading'
		);
	}

	/**
	 * @test
	 */
	public function can_add_field_to_hash()
	{
		$hash = ['country' => 'Africa', 'age' => 12];

		$this->redis->hmset('person', $hash);
		$this->redis->hset('person', 'color', 'black');
		$hashRead = $this->redis->hgetall('person');

		$this->assertSame(['country' => 'Africa', 'age' => '12', 'color' => 'black'], $hashRead);
	}

	/**
	 * @test
	 */
	public function can_remove_field_from_hash()
	{
		$hash = ['country' => 'Africa', 'age' => 12];

		$this->redis->hmset('person', $hash);
		$this->redis->hdel('person', ['age']);
		$hashRead = $this->redis->hgetall('person');

		$this->assertSame(['country' => 'Africa'], $hashRead);
	}

	public function tearDown()
	{
		$this->redis->flushall();
		$this->redis->flushdb();
	}


}