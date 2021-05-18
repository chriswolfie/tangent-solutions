<?php

namespace App\Repositories\Eloquent;

use App\Models\Users as ModelsUsers;
use App\Repositories\Contracts\Users as UsersContract;
use Illuminate\Support\Collection;

class Users extends Base implements UsersContract
{
    public function __construct(ModelsUsers $model)
    {
        parent::__construct($model);
    }

    public function allUsers() : Collection
    {
        return $this->model->all();
    }
}