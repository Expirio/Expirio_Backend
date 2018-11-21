<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EncDecTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEncrypt()
    {
    	$password = 'aff49eb3-d81c-4f2a-93f0-2f96ec9cb3bb';
    	$response = $this->call('GET', '/encrypt/'.$password);    	

    	$response->assertSeeText('OK');
    }

    public function testDecrypt()
    {
    	$password = 'aff49eb3-d81c-4f2a-93f0-2f96ec9cb3bb';
    	$response = $this->call('GET', '/decrypt/'.$password);

    	$response->assertSeeText('OK');
    }
}
