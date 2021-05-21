<?php

namespace Tests\Feature;

use App\Repositories\Contracts\Users as UsersContract;
use App\Repositories\Eloquent\Users as UsersEloquent;
use Tests\MigrateOnce;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use MigrateOnce;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freshMigration();

        app()->bind(UsersContract::class, UsersEloquent::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $users_model = app()->make(UsersContract::class);
        // $all_entries = $users_model->fetchAllEntries();
        // echo $all_entries->count();

        $this->assertTrue(true);
    }
}
