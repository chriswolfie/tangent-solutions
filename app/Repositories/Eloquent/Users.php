<?php

namespace App\Repositories\Eloquent;

use App\Models\Users as UsersModel;
use App\Repositories\Contracts\Users as UsersContract;
use Illuminate\Support\Collection;
use stdClass;

class Users extends Base implements UsersContract
{
    public function __construct(UsersModel $model)
    {
        parent::__construct($model);
    }

    public function allUsers() : Collection
    {
        return $this->model->all();
    }

    public function createUser(string $full_name, string $email) : stdClass
    {
        $user = $this->create([
            'full_name' => $full_name,
            'email' => $email
        ]);
        return json_decode( json_encode( $user->toArray() ) );
    }

    // public function fetchUser(int $user_id) : stdClass
    // {
    //     var_dump($this->model->find(44));
    //     exit();
    //     return json_decode( json_encode( $user->toArray() ) );
    // }
}