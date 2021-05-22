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
        // grab an API key...
        $api_key = $this->fetchAnApiKey();

        // basic validation tests first...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(401);
        $this->checkResponseResource($data, ['message']);

        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['title', 'content', 'category_id']);

        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post', ['title' => 'B', 'content' => 'AB']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['title', 'content', 'category_id']);

        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post', ['title' => 'True Post Request', 'content' => 'This is some content', 'category_id' => 22]);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['category_id']);

        // should add this post...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post', ['title' => 'True Post Request', 'content' => 'This is some content', 'category_id' => 3]);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(201);
        $this->checkResponseResource($data, ['post_id', 'post_title', 'post_content', 'user_id', 'category_id']);

        // check for uniqueness of the title...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post', ['title' => 'True Post Request', 'content' => 'This is some content for another post', 'category_id' => 1]);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['title']);
    }

    public function test_post_api_get()
    {
        // grab an API key...
        $api_key = $this->fetchAnApiKey();

        // fetch an existing entry...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post/2');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['post_id', 'post_title', 'post_content', 'user_id', 'category_id']);

        // create and fetch...
        // already tested this endpoint above...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/post', ['title' => 'True Post Request', 'content' => 'This is some content', 'category_id' => 3]);
        // try fetch it...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post/6');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['post_id', 'post_title', 'post_content', 'user_id', 'category_id']);
    }

    public function test_post_api_put()
    {
        // grab an API key...
        $api_key = $this->fetchAnApiKey();

        // basic validation tests first...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/4');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(401);
        $this->checkResponseResource($data, ['message']);

        // posts that don't exist...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/999');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(401);
        $this->checkResponseResource($data, ['message']);

        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/999');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);

        // request validations...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/4', ['title' => 'B', 'content' => 'AB']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['title', 'content']);
        
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/4', ['title' => 'True Post Request', 'content' => 'This is some content', 'category_id' => 999]);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['category_id']);
        
        // should update this post...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/4', ['title' => 'True Post Request', 'content' => 'This is some content', 'category_id' => 1]);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['post_id', 'post_title', 'post_content', 'user_id', 'category_id']);

        // check same request on a post that doesn't exist...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/999', ['title' => 'True Post Request', 'content' => 'This is some content', 'category_id' => 1]);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);

        // check for uniqueness of the title...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/4', ['title' => 'True Post Request']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['post_id', 'post_title', 'post_content', 'user_id', 'category_id']);

        // this won't work for another post...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/post/2', ['title' => 'True Post Request']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['title']);
    }

    public function test_post_api_delete()
    {
        // grab an API key...
        $api_key = $this->fetchAnApiKey();

        // basic validation tests first...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->deleteJson('/api/v1/post/3');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(401);
        $this->checkResponseResource($data, ['message']);

        // posts that don't exist...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->deleteJson('/api/v1/post/999');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(204);
        $this->assertEquals($data, null);

        // posts that exist...
        $response = $this
            ->withHeaders([ 'api_key' => $api_key, 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->deleteJson('/api/v1/post/3');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(204);
        $this->assertEquals($data, null);

        // check it deleted...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/post');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->assertEquals(count($data), 4, 'There should be 4 posts in the database');
        $this->checkResponseResource($data[0], ['post_id', 'post_title', 'post_content', 'user_id', 'category_id']);
    }

}
