<?php

namespace App\Repositories\Eloquent;

use App\Models\Users as UsersModel;
use App\Repositories\Contracts\Users as UsersContract;

class Users extends Base implements UsersContract
{
    public function __construct(UsersModel $model)
    {
        parent::__construct($model, ['full_name', 'email']);
    }
}