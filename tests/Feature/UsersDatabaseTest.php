<?php

namespace Tests\Feature;

use App\Models\Users;
use App\Repositories\Contracts\Users as UsersContract;
use App\Repositories\Eloquent\Users as UsersEloquent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersDatabaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->bind(UsersContract::class, UsersEloquent::class);
    }

    public function test_adding_a_user()
    {
        $users_model = app()->make(UsersContract::class);
        $user = $users_model->createEntry([
            'full_name' => 'Test User',
            'email' => 'test@email.com'
        ]);
        $this->assertNotEquals($user, null, 'Added user should not be null');
        $this->assertEquals($user->id, 1, 'Added user ID is not correct');
    }

    public function test_user_exists_and_is_fetchable()
    {
        // seed a user...
        Users::factory()->create();

        $users_model = app()->make(UsersContract::class);
        $user = $users_model->fetchSingleEntry(1);
        $this->assertNotEquals($user, null, 'Added user should not be null');
        $this->assertEquals($user->id, 1, 'Added user ID is not correct');
    }

    public function test_can_fetch_multiple_users()
    {
        // seed 3 users...
        Users::factory()->count(3)->create();

        $users_model = app()->make(UsersContract::class);
        $users = $users_model->fetchAllEntries();
        $this->assertEquals(count($users), 3, 'There should be 3 users in the users table');
    }

    public function test_user_can_be_updated()
    {
        // seed a user...
        Users::factory()->create();

        $users_model = app()->make(UsersContract::class);
        $users_model->updateEntry(1, ['full_name' => 'New Name', 'email' => 'new@email.com']);

        $user = $users_model->fetchSingleEntry(1);
        $this->assertEquals($user->full_name, 'New Name', 'User full name has not been updated');
        $this->assertEquals($user->email, 'new@email.com', 'User email has not been updated');
    }

    public function test_user_can_be_removed()
    {
        // seed 2 users...
        Users::factory()->count(2)->create();

        $users_model = app()->make(UsersContract::class);
        $users_model->removeEntry(1);

        $users = $users_model->fetchAllEntries();
        $this->assertEquals(count($users), 1, 'There should only be 1 user in the users table');
    }

    public function test_exercising_email_uniqueness_check()
    {
        // seed 2 users...
        Users::factory()->count(2)->create();

        $users_model = app()->make(UsersContract::class);
        $users_model->updateEntry(1, ['email' => 'first@email.com']);
        $users_model->updateEntry(2, ['email' => 'second@email.com']);

        $this->assertEquals($users_model->valueIsUnique('new@email.com', 'email'), true, 'This email should not exist yet');
        $this->assertEquals($users_model->valueIsUnique('first@email.com', 'email'), false, 'This email should exist');
        $this->assertEquals($users_model->valueIsUnique('first@email.com', 'email', 1), true, 'This email should exist for this user only');
    }

    public function test_checking_user_value_validity()
    {
        // seed a user...
        Users::factory()->create();

        $users_model = app()->make(UsersContract::class);
        $users_model->updateEntry(1, ['full_name' => 'Johnny Cool']);

        $this->assertEquals($users_model->valueExists('Johnny Cool', 'full_name'), true, 'This value should exist');
        $this->assertEquals($users_model->valueExists('Johnny Not Cool', 'full_name'), false, 'This value should not exist');
    }

    public function test_user_is_reachable_via_api_key()
    {
        // seed a user...
        Users::factory()->create();

        $users_model = app()->make(UsersContract::class);
        $user = $users_model->fetchSingleEntry(1);
        $api_key = $user->api_key;

        $this->assertNotEquals($api_key, '', 'This API key value should not be blank');

        $user = $users_model->getUserWithApiKey($api_key);

        $this->assertNotEquals($user->api_key, '', 'This fetched API key value should not be blank');
    }
}
