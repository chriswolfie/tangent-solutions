<?php

namespace App\Repositories\Eloquent;

use App\Models\Posts as PostsModel;
use App\Repositories\Contracts\Posts as PostsContract;
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

    /**
     * @return mixed Either null on failure, or stdClass of the fetched entry.
     */
    public function fetchSingleEntry(int $id) : ?stdClass
    {
        $post = $this->model->find($id);
        if (!$post) {
            return null;
        }
        return json_decode( json_encode( $post ) );
    }
}