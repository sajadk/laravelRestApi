<?php

use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [App\Http\Controllers\Api\UserAuthController::class, 'register']);
Route::post('login', [App\Http\Controllers\Api\UserAuthController::class, 'login']);

Route::prefix('/v1')->middleware('auth:api')->group(function () {
   Route::get('/user', [App\Http\Controllers\Api\UserAuthController::class, 'userInfo']);
   Route::get('/userdata/{id}', [App\Http\Controllers\Api\UserAuthController::class, 'userData']);
   Route::put('/user-update/{id}', [App\Http\Controllers\Api\UserAuthController::class , 'update']);
   Route::delete('/user/{id}', [App\Http\Controllers\Api\UserAuthController::class , 'delete']);
   Route::post('/logout', [App\Http\Controllers\Api\UserAuthController::class, 'logout']);
});
