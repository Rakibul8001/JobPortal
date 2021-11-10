<?php

use App\Http\Controllers\JobController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//public routes
Route::post('/register',[UserController::class, 'register']);
Route::post('/login',[UserController::class, 'login']);
Route::post('/logout/user/{id}',[UserController::class, 'logout']);

//protected routes
Route::group(['middleware'=>['auth:sanctum']], function(){
    Route::get('/users',[UserController::class, 'index']);
    Route::apiResource('jobs',JobController::class);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
