<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BadRequestsTest extends TestCase
{
    public function test_making_bad_requests()
    {
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/some/bad/url');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
    }
}
