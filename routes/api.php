<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users/new-users-to-chat', [UserController::class, 'newUsersToChat']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('users.chats', \App\Http\Controllers\UserChatController::class);
    Route::apiResource('messages', \App\Http\Controllers\MessageController::class);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/sanctum/token', [AuthController::class, 'mobileLogin']);
Route::apiResource('/languages', \App\Http\Controllers\LanguageController::class);
