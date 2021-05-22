<?php

namespace App\Repositories\Contracts;

interface Categories extends Base 
{
    public function removeCategory(int $entry_id) : void;
}