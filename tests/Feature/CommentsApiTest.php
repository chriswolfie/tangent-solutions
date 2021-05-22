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

    public function test_comment_api_post()
    {
        // grab an API key...
        $api_key = $this->fetchAnApiKey();

        // check broken post...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post/999/comment');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);

        // check authorisation...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post/1/comment');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(401);
        $this->checkResponseResource($data, ['message']);

        // validation checks...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post/1/comment');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['content']);

        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post/1/comment', ['content' => 'ABC']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['content']);

        // legitimate request...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post/1/comment', ['content' => 'This is a test comment.']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(201);
        $this->checkResponseResource($data, ['comment_id', 'content', 'user']);
        $this->checkResponseResource($data['user'], ['user_id', 'full_name', 'email']);
    }

    public function test_comment_api_get()
    {
        // grab an API key...
        $api_key = $this->fetchAnApiKey();

        // fetch an existing one...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post/1/comment/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['comment_id', 'content', 'user']);
        $this->checkResponseResource($data['user'], ['user_id', 'full_name', 'email']);

        // fetch a mis-aligned comment...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post/1/comment/999');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);

        // create-and-fetch...
        // this creation process has already been tested...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post/1/comment', ['content' => 'This is a test comment.']);
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post/1/comment/6');
        $response->assertStatus(200);
        $data = json_decode( $response->content(), true );
        $this->checkResponseResource($data, ['comment_id', 'content', 'user']);
        $this->checkResponseResource($data['user'], ['user_id', 'full_name', 'email']);
    }

    public function test_comment_api_put()
    {
        // grab an API key...
        $api_key = $this->fetchAnApiKey();

        // check broken post...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/999/comment/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);

        // check authorisation...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/1/comment/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(401);
        $this->checkResponseResource($data, ['message']);

        // validation checks...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/1/comment/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['content']);

        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/1/comment/1', ['content' => 'ABC']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['content']);

        // legitimate request...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/1/comment/1', ['content' => 'This is a new comment.']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['comment_id', 'content', 'user']);
        $this->checkResponseResource($data['user'], ['user_id', 'full_name', 'email']);

        // checking existence of comment...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/1/comment/999', ['content' => 'This is a test comment.']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);

        // check the comment was updated...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post/1/comment/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['comment_id', 'content', 'user']);
        $this->checkResponseResource($data['user'], ['user_id', 'full_name', 'email']);
        $this->assertEquals($data['content'], 'This is a new comment.', 'The comment was not updated correctly');
    }

    public function test_post_api_delete()
    {
        // grab an API key...
        $api_key = $this->fetchAnApiKey();

        // auth test first...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->deleteJson('/api/v1/post/1/comment/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(401);
        $this->checkResponseResource($data, ['message']);

        // comments and posts that don't exist...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->deleteJson('/api/v1/post/999/comment/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);

        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->deleteJson('/api/v1/post/1/comment/999');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);

        // legitimate request...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->deleteJson('/api/v1/post/1/comment/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(204);
        $this->assertEquals($data, null);

        // see if we can see this comment...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post/1/comment/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);
    }
}
