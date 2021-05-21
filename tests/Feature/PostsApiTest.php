<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PersonalAssistant;
use Tests\TestCase;

class PostsApiTest extends TestCase
{
    use RefreshDatabase,
    PersonalAssistant;

    protected function setUp(): void
    {
        parent::setUp();
        
        // seed the whole DB...
        $this->seedEntireDatabase();
    }

    public function test_post_api_list()
    {
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post');
        $data = json_decode( $response->content(), true );

        $response->assertStatus(200);
        $this->assertEquals(count($data), 5, 'There should be 5 posts in the database');
        $this->checkResponseResource($data[0], ['post_id', 'post_title', 'post_content', 'user_id', 'category_id']);
    }

    public function test_post_api_post()
    {
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post');
        $response->assertStatus(401);
        $data = json_decode( $response->content(), true );
        $this->checkResponseResource($data, ['message']);

        $api_key = $this->fetchAnApiKey();
        print_r($api_key);

        print_r($data);
    }

}
