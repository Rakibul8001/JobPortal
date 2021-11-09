<?php

use App\Http\Controllers\JobController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//public routes
Route::post('/register',[UserController::class, 'register'])->name('register');
Route::get('/users',[UserController::class, 'index'])->name('user.index');
Route::apiResource('jobs',JobController::class);

//protected routes
Route::group(['middleware'=>['admin']], function(){
    Route::get('/jobs',[JobController::class,'index'])->name('jobs.index');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
