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

	public function returned_value_for_no_existing_key()
	{
		$result = $this->redis->hgetall('this doesnt exist');
		$this->assertNull($result);
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

	/**
	 * @test
	 */
	public function can_check_exist_a_key()
	{
		$hash = ['country' => 'Africa', 'age' => 12];

		$this->redis->hmset('person', $hash);

		$this->assertSame(1, $this->redis->exists('person'));
		$this->assertSame(0, $this->redis->exists('non_existing'));
	}

	/**
	 * @test
	 */
	public function can_check_specific_fields_of_hash()
	{
		$hash = ['country' => 'Africa', 'age' => 12];

		$this->redis->hmset('person', $hash);

		$this->assertSame(1, $this->redis->hexists('person', 'age'));
		$this->assertSame(0, $this->redis->hexists('person', 'non_existing'));
		$this->assertSame('Africa', $this->redis->hget('person', 'country'));
	}

	/**
	 * @test
	 */
	public function can_remove_key()
	{
		$hash = ['country' => 'Africa', 'age' => 12];

		$this->redis->hmset('person', $hash);

		$this->redis->del(['person']);
		$this->assertSame(0, $this->redis->exists('person'));

	}

	/**
	 * @test
	 */
	public function can_set_expiration_for_data()
	{
		$hash = ['country' => 'Africa', 'age' => 12];

		$this->redis->hmset('person', $hash);
		$this->redis->expire('person', 2);

		$this->assertSame(1, $this->redis->exists('person'), 'still exist');
		sleep(3);
		$this->assertSame(0, $this->redis->exists('person'), 'it doesnt exist anymore');
	}

	public function tearDown()
	{
		$this->redis->flushall();
		$this->redis->flushdb();
	}


}