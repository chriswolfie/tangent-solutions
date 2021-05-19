<?php

namespace App\Repositories\Eloquent;

use App\Models\Users as UsersModel;
use App\Repositories\Contracts\Users as UsersContract;
use Illuminate\Support\Collection;
use stdClass;

class Users implements UsersContract
{
    private $model;
    public function __construct(UsersModel $model)
    {
        $this->model = $model;
    }

    public function allUsers() : Collection
    {
        return $this->model->all();
    }

    public function createUser(string $full_name, string $email) : stdClass
    {
        $user = $this->model->create([
            'full_name' => $full_name,
            'email' => $email
        ]);
        return json_decode( json_encode( $user->toArray() ) );
    }

    /**
     * @return mixed Either null on failure, or stdClass of the fetched entry.
     */
    public function fetchUser(int $user_id) : ?stdClass
    {
        $user = $this->model->find($user_id);
        if (!$user) {
            return null;
        }
        return json_decode( json_encode( $user->toArray() ) );
    }

    /**
     * @return mixed Either null on failure, or stdClass of the updated entry.
     */
    public function updateUser(int $user_id, string $full_name = '', string $email = '') : ?stdClass
    {
        $user = $this->model->find($user_id);
        if (!$user) {
            return null;
        }

        if ($full_name != '' || $email != '') {
            $user->full_name = ($full_name != '' ? $full_name : $user->full_name);
            $user->email = ($email != '' ? $email : $user->email);
            $user->save();
        }

        return json_decode( json_encode( $user->toArray() ) );
    }

    public function removeUser(int $user_id) : void
    {
        $user = $this->model->find($user_id);
        if ($user) {
            $user->delete();
        }
    }
}