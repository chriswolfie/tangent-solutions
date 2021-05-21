<?php

namespace Tests;

use App\Models\Categories;
use App\Models\Comments;
use App\Models\Posts;
use App\Models\Users;

trait PersonalAssistant
{
    public function seedEntireDatabase() : void
    {
        for ($i=0; $i<5; $i++) {
            $user = Users::factory()->create();
            $category = Categories::factory()->create();
            $post = Posts::factory()
                ->for($user)
                ->for($category)
                ->create();
            Comments::factory()
                ->for($user)
                ->for($post)
                ->create();
        }
    }

    public function checkResponseResource(array $data_item, array $field_key_list) : void
    {
        $total_fields = count($field_key_list);
        $this->assertEquals(count($data_item), $total_fields, 'There should be '. $total_fields .' item(s) here');
        foreach ($field_key_list as $field_key) {
            $this->assertEquals(isset($data_item[$field_key]), true, 'The ' . $field_key . ' field is missing');
        }
    }
}