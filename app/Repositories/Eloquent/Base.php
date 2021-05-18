<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Base 
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}