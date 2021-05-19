<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use stdClass;

interface Users
{
    public function allUsers() : Collection;
    public function createUser(string $full_name, string $email) : stdClass;

    /**
     * @return mixed Either null on failure, or stdClass of the fetched entry.
     */
    public function fetchUser(int $user_id) : ?stdClass;

    /**
     * @return mixed Either null on failure, or stdClass of the updated entry.
     */
    public function updateUser(int $user_id, string $full_name = '', string $email = '') : ?stdClass;
    public function removeUser(int $user_id) : void;
}