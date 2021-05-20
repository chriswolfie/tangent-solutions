<?php

namespace App\Providers;

use App\Repositories\Contracts\Categories as CategoriesContract;
use App\Repositories\Contracts\Comments as CommentsContract;
use App\Repositories\Contracts\Posts as PostsContract;
use App\Repositories\Contracts\Users as UsersContract;
use App\Repositories\Eloquent\Categories as CategoriesEloquent;
use App\Repositories\Eloquent\Comments as CommentsEloquent;
use App\Repositories\Eloquent\Posts as PostsEloquent;
use App\Repositories\Eloquent\Users as UsersEloquent;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class TangentServiceProvider extends ServiceProvider
{
    public $bindings = [
        CategoriesContract::class => CategoriesEloquent::class,
        CommentsContract::class => CommentsEloquent::class,
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
        JsonResource::withoutWrapping();
    }
}
