<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleController;
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
Route::post("login",[AuthController::class,"login"]);
Route::post("register",[AuthController::class,"register"]);
Route::post("logout",[AuthController::class,"logout"])->middleware("auth:sanctum");
Route::middleware("auth:sanctum")->group(function(){
    Route::apiResource("roles",RoleController::class);
    Route::apiResource("user_roles",UserRoleController::class);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
