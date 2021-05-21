<?php

namespace App\Repositories\Eloquent;

use App\Models\Users as UsersModel;
use App\Repositories\Contracts\Users as UsersContract;
use stdClass;

class Users extends Base implements UsersContract
{
    public function __construct(UsersModel $model)
    {
        parent::__construct($model, ['full_name', 'email', 'api_key']);
    }

    // override...
    // we have hashes...
    public function createUserEntry(array $attributes) : ?stdClass
    {
        $attributes['api_key'] = sha1(serialize($attributes)) . sha1(microtime(true));
        return parent::createEntry($attributes);
    }

    public function getUserWithApiKey(string $api_key) : ?stdClass
    {
        $user = $this->model->where('api_key', $api_key)->first();
        if (!$user) {
            return null;
        }
        return json_decode( json_encode( $user->toArray() ) );
    }
}