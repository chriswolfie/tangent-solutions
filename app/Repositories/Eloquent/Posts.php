<?php

namespace App\Repositories\Eloquent;

use App\Models\Posts as PostsModel;
use App\Repositories\Contracts\Posts as PostsContract;

class Posts extends Base implements PostsContract
{
    public function __construct(PostsModel $model)
    {
        parent::__construct($model, ['title', 'content', 'user_id', 'category_id']);
    }
}