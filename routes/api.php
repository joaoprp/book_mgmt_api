<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\IndexController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'create']);
    Route::patch('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);

    Route::get('/indexes', [IndexController::class, 'index']);
    Route::post('/indexes', [IndexController::class, 'create']);
    Route::patch('/indexes/{id}', [IndexController::class, 'update']);
    Route::delete('/indexes/{id}', [IndexController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
