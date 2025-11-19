<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoItemController;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//? Rotas de Autenticação

// Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

//? Rotas de Usuários

Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('user', UserController::class)->except('store', 'index');
});

//? Rotas de Listas

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('list', TodoListController::class);
});

//? Rotas de Itens

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('item', TodoItemController::class)->only('store', 'update', 'destroy');
});
