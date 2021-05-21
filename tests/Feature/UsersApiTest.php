<?php

namespace Tests\Feature;

use App\Models\Users;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\PersonalAssistant;
use Tests\TestCase;

class UsersApiTest extends TestCase
{
    use RefreshDatabase,
    PersonalAssistant;

    protected function setUp(): void
    {
        parent::setUp();
        
        // seed the whole DB...
        $this->seedEntireDatabase();
    }

    public function test_user_api_list()
    {
        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->get('/api/v1/user');
        $data = json_decode( $response->content(), true );

        $response->assertStatus(200);
        $this->assertEquals(count($data), 5, 'There should be 5 users in the database');
    }

    public function test_user_api_post()
    {
        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->postJson('/api/v1/user', []);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data['errors'], ['full_name', 'email']);

        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->postJson('/api/v1/user', [
                'full_name' => 'Cool Person'
            ]);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data['errors'], ['email']);

        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->postJson('/api/v1/user', [
                'full_name' => 'Invalid',
                'email' => 'bademail'
            ]);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data['errors'], ['full_name', 'email']);

        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->postJson('/api/v1/user', [
                'full_name' => 'Valid Test Person',
                'email' => 'valid@emailaddress.com'
            ]);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(201);
        $this->checkResponseResource($data, ['user_id', 'full_name', 'email']);
        $this->assertEquals($data['user_id'], 6, 'The user ID here should be sequential');

        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->postJson('/api/v1/user', [
                'full_name' => 'A Different Person',
                'email' => 'valid@emailaddress.com'
            ]);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data['errors'], ['email']);
    }

    public function test_user_api_get()
    {
        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->get('/api/v1/user/5');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['user_id', 'full_name', 'email']);
        $this->assertEquals($data['user_id'], 5, 'User ID is not sequential here');

        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->get('/api/v1/user/20');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);
    }

    public function test_user_api_put()
    {
        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->putJson('/api/v1/user/100', []);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message']);

        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->putJson('/api/v1/user/1', []);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['user_id', 'full_name', 'email']);
        $this->assertEquals($data['user_id'], 1, 'User ID is not sequential here');

        // update each piece individually...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/user/1', [ 'full_name' => 'Testing New Name' ]);
        $response->assertStatus(200);
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/user/1', [ 'email' => 'new@email.com' ]);
        $response->assertStatus(200);

        $response = $this
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])
            ->get('/api/v1/user/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['user_id', 'full_name', 'email']);
        $this->assertEquals($data['user_id'], 1, 'There is something wrong with the user ID');
    }

    public function test_user_api_delete()
    {
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->delete('/api/v1/user/4');
        $response->assertStatus(204);

        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/user');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->assertEquals(count($data), 4, 'There should be 4 users in the database');
    }
}
