<?php

// use Illuminate\Http\Request;

use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Middleware\PostExists;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->name('api.v1.')->group(function() {

    Route::apiResource('user', UserController::class);
    Route::apiResource('post.comment', CommentController::class)->middleware(PostExists::class);

});
