<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController; // Import the new controller

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/admin-login', [AuthController::class, 'adminLogin']);
Route::post('/user-login', [AuthController::class, 'userLogin']);
Route::post('/register', [AuthController::class, 'register']); // added for registering -kirk

// Protected routes that require a token
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});