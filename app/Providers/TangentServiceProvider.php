<?php

namespace App\Providers;

use App\Http\Resources\User as UserResource;
use App\Repositories\Contracts\Posts as PostsContract;
use App\Repositories\Contracts\Users as UsersContract;
use App\Repositories\Eloquent\Posts as PostsEloquent;
use App\Repositories\Eloquent\Users as UsersEloquent;
use Illuminate\Support\ServiceProvider;

class TangentServiceProvider extends ServiceProvider
{
    public $bindings = [
        PostsContract::class => PostsEloquent::class,
        UsersContract::class => UsersEloquent::class,

    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        UserResource::withoutWrapping();
    }
}
