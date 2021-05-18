<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface Users
{
    public function allUsers() : Collection;
}