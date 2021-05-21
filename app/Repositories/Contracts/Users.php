<?php

namespace App\Repositories\Contracts;

use stdClass;

interface Users extends Base
{
    public function createUserEntry(array $attributes) : ?stdClass;
    public function getUserWithApiKey(string $api_key) : ?stdClass;
}