<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => false,
        'message' => 'Akses tidak diperbolehkan'
    ], 401);
})->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::apiResource('posts', PostController::class, ['except' => ['index', 'show']]);
    Route::apiResource('comments', CommentController::class, ['only' => ['store', 'update', 'destroy']]);
});

Route::apiResource('comments', CommentController::class, ['only' => ['index']]);
Route::apiResource('posts', PostController::class, ['only' => ['index', 'show']]);

