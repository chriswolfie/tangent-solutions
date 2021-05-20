<?php

// use Illuminate\Http\Request;

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Middleware\PostValidationAndFetch;
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

    Route::apiResource('category', CategoryController::class);
    Route::apiResource('user', UserController::class);
    Route::apiResource('post', PostController::class);
    Route::apiResource('post.comment', PostController::class)->middleware(PostValidationAndFetch::class);

});
