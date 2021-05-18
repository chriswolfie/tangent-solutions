<?php

// use Illuminate\Http\Request;

use App\Http\Controllers\Api\V1\UsersController;
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

    Route::apiResources([
        'users' => UsersController::class,
    ]);

    // comment "helper" routes...
    Route::match(['get'], '/users/{user}/comments', [UsersController::class, 'commentsAction'])->name('users.comments');
});
