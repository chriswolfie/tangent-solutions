<?php

namespace App\Repositories\Contracts;

use App\Http\Resources\User as UserResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use stdClass;

interface Users
{
    public function allUsers() : Collection;
    public function createUser(string $full_name, string $email) : stdClass;
    // public function fetchUser(int $user_id) : stdClass;
}