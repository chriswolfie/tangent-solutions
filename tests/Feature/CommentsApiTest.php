<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PersonalAssistant;
use Tests\TestCase;

class CommentsApiTest extends TestCase
{
    use RefreshDatabase,
    PersonalAssistant;

    protected function setUp(): void
    {
        parent::setUp();
        
        // seed the whole DB...
        $this->seedEntireDatabase();
    }

    public function test_comment_api_list()
    {
        // existing post...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post/1/comment');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->assertEquals(count($data), 1, 'There should be 1 comment for this post in the database');
        $this->checkResponseResource($data[0], ['comment_id', 'content', 'user']);
        $this->checkResponseResource($data[0]['user'], ['user_id', 'full_name', 'email']);

        // non-existing post...
        $response = $this
        ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post/999/comment');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);
    }
}
