<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PersonalAssistant;
use Tests\TestCase;

class CategoriesApiTest extends TestCase
{
    use RefreshDatabase,
    PersonalAssistant;

    protected function setUp(): void
    {
        parent::setUp();
        
        // seed the whole DB...
        $this->seedEntireDatabase();
    }

    public function test_category_api_list()
    {
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/category');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->assertEquals(count($data), 5, 'There should be 5 categories in the database');
        $this->checkResponseResource($data[0], ['category_id', 'label']);
    }

    public function test_category_api_post()
    {
        // basic validation tests first...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/category');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['label']);

        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/category', ['label' => 'A']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['label']);

        // legitimate request...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/category', ['label' => 'Real Category']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(201);
        $this->checkResponseResource($data, ['category_id', 'label']);

        // make sure it's there...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/category');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->assertEquals(count($data), 6, 'There should be 6 categories in the database');

        // uniqueness...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/category', ['label' => 'Real Category']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['label']);
    }

    public function test_category_api_get()
    {
        // fetch an existing entry...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/category/2');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['category_id', 'label']);

        // non-existent...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/category/999');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);

        // create-and-fetch...
        // tested this above already...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/category', ['label' => 'Real Category']);
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/category/6');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['category_id', 'label']);
    }

    public function test_category_api_put()
    {
        // basic validations first...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/category/2');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['label']);

        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/category/2', ['label' => 'A']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['label']);

        // legitimate request...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/category/2', ['label' => 'New Label']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['category_id', 'label']);

        // check it updated...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/category/2');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['category_id', 'label']);
        $this->assertEquals($data['label'], 'New Label', 'This label should have been updated');

        // check uniqueness...
        // this should work...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/category/2', ['label' => 'New Label']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->checkResponseResource($data, ['category_id', 'label']);

        // ...but this shouldn't...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/category/3', ['label' => 'New Label']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(422);
        $this->checkResponseResource($data, ['message', 'errors']);
        $this->checkResponseResource($data['errors'], ['label']);

        // check non-existent category...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->putJson('/api/v1/category/999', ['label' => 'New Label']);
        $data = json_decode( $response->content(), true );
        $response->assertStatus(404);
        $this->checkResponseResource($data, ['message']);
    }

    public function test_category_api_delete()
    {
        // check on category that doesn't exist...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->deleteJson('/api/v1/category/999');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(204);
        $this->assertEquals($data, null);

        // check all categories are there...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/category');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->assertEquals(count($data), 5, 'There should be 5 categories in the database');

        // check linked category...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->deleteJson('/api/v1/category/1');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(204);
        $this->assertEquals($data, null);

        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/category');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->assertEquals(count($data), 5, 'There should be 5 categories in the database');

        // now create an unlinked category...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->postJson('/api/v1/category', ['label' => 'Real Category']);
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/category');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->assertEquals(count($data), 6, 'There should be 6 categories in the database');

        // and delete the unlinked category...
        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->deleteJson('/api/v1/category/6');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(204);
        $this->assertEquals($data, null);

        $response = $this
            ->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])
            ->get('/api/v1/category');
        $data = json_decode( $response->content(), true );
        $response->assertStatus(200);
        $this->assertEquals(count($data), 5, 'There should be 5 categories in the database');
    }
}
