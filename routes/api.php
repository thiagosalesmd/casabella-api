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


Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name("login");
Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [App\Http\Controllers\AuthController::class, 'resetPasswordByToken']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/change-password', [App\Http\Controllers\AuthController::class, 'changePassword']);
    Route::post('/me', [App\Http\Controllers\AuthController::class, 'me']);
    Route::post('/send-mail', [App\Http\Controllers\EmailController::class, 'sendEmailTo']);
    
    Route::prefix('/users')->group(function () {
        Route::get('/', [App\Http\Controllers\UserController::class, 'index']);
        Route::post('/', [App\Http\Controllers\UserController::class, 'store']);
        Route::post('/{id}', [App\Http\Controllers\UserController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\UserController::class, 'destroy']);
        Route::post('/{id}/avatar', [App\Http\Controllers\UserController::class, 'avatar']);
        Route::post('/{id}/attachments', [App\Http\Controllers\UserController::class, 'attachments']);
    });

    Route::prefix('/groups')->group(function () {
        Route::get('/', [App\Http\Controllers\GroupController::class, 'index']);
        Route::post('/', [App\Http\Controllers\GroupController::class, 'store']);
        Route::post('/{id}', [App\Http\Controllers\GroupController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\GroupController::class, 'destroy']);
    });
    
    Route::prefix('/permissions')->group(function () {
        Route::get('/', [App\Http\Controllers\PermissionController::class, 'index']);
        Route::post('/', [App\Http\Controllers\PermissionController::class, 'store']);
        Route::post('/{id}', [App\Http\Controllers\PermissionController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\PermissionController::class, 'destroy']);
    });
});
