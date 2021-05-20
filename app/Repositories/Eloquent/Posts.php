<?php

namespace App\Repositories\Eloquent;

use App\Models\Posts as PostsModel;
use App\Repositories\Contracts\Posts as PostsContract;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use stdClass;

class Posts implements PostsContract
{
    private $model;
    public function __construct(PostsModel $model)
    {
        $this->model = $model;
    }

    public function fetchAllEntries() : Collection
    {
        return $this->model->all();
    }

    public function fetchSingleEntry(int $id) : ?stdClass
    {
        $post = $this->model->find($id);
        if (!$post) {
            return null;
        }
        return json_decode( json_encode( $post ) );
    }

    public function createEntry(array $attributes) : ?stdClass
    {
        $post = false;
        try {
            $post = $this->model->create($attributes);
        }
        catch (QueryException $e) {
            return null;
        }
        return json_decode( json_encode( $post ) );
    }

    public function updateEntry(int $entry_id, array $attributes) : ?stdClass
    {
        $post = $this->model->find($entry_id);
        if (!$post) {
            return null;
        }

        $legal_data_keys = ['title', 'content', 'user_id', 'category_id'];
        try {
            foreach ($attributes as $attribute_key => $attribute_value) {
                if (!in_array($attribute_key, $legal_data_keys)) {
                    // continue;
                }
                $post->{$attribute_key} = $attribute_value;
            }
            $post->save();
        }
        catch (QueryException $e) {
            return null;
        }

        return json_decode( json_encode( $post ) );
    }

    public function removeEntry(int $entry_id) : void
    {
        $post = $this->model->find($entry_id);
        if ($post) {
            $post->delete();
        }
    }
}