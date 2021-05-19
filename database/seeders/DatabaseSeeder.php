<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\Comments;
use App\Models\Posts;
use App\Models\Users;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i<5; $i++) {
            $user = Users::factory()->create();
            $category = Categories::factory()->create();
            $post = Posts::factory()
                ->for($user)
                ->for($category)
                ->create();
            $comment = Comments::factory()
                ->for($user)
                ->for($post)
                ->create();
        }
    }
}
