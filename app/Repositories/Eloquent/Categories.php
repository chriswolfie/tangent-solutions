<?php

namespace App\Repositories\Eloquent;

use App\Models\Categories as CategoriesModel;
use App\Repositories\Contracts\Categories as CategoriesContract;

class Categories extends Base implements CategoriesContract
{
    public function __construct(CategoriesModel $model)
    {
        parent::__construct($model, ['label']);
    }

    // we need a custom check here to make sure we're not removing
    // categories that posts are currently using...
    public function removeCategory(int $entry_id) : void
    {
        $category = $this->model->find($entry_id);
        if ($category) {
            $posts_count = $category->posts()->count();
            if ($posts_count == 0) {
                parent::removeEntry($entry_id);
            }
        }
    }
}