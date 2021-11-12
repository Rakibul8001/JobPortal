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
    Route::apiResource('users',UserController::class);
    Route::apiResource('jobs',JobController::class);
});

