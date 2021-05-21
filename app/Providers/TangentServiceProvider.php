<?php

namespace App\Providers;

use App\Contracts\LogWriter as LogWriterContract;
use App\LogWriters\DatabaseWriter;
use App\LogWriters\FileWriter;
use App\Repositories\Contracts\ApiLogs as ApiLogsContract;
use App\Repositories\Contracts\Categories as CategoriesContract;
use App\Repositories\Contracts\Comments as CommentsContract;
use App\Repositories\Contracts\Posts as PostsContract;
use App\Repositories\Contracts\Users as UsersContract;
use App\Repositories\Eloquent\ApiLogs as ApiLogsEloquent;
use App\Repositories\Eloquent\Categories as CategoriesEloquent;
use App\Repositories\Eloquent\Comments as CommentsEloquent;
use App\Repositories\Eloquent\Posts as PostsEloquent;
use App\Repositories\Eloquent\Users as UsersEloquent;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class TangentServiceProvider extends ServiceProvider
{
    public $bindings = [
        ApiLogsContract::class => ApiLogsEloquent::class,
        CategoriesContract::class => CategoriesEloquent::class,
        CommentsContract::class => CommentsEloquent::class,
        LogWriterContract::class => DatabaseWriter::class,
        // LogWriterContract::class => FileWriter::class,
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
