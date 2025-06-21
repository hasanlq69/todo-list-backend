<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// routes/api

use App\Http\Controllers\Api\TodoController;

Route::post('/todos', [TodoController::class, 'store']);
Route::get('/todos', [TodoController::class, 'index']);

// Route untuk mengekspor data Todo ke Excel
Route::get('/todos/export', [TodoController::class, 'export']);

// Route untuk chart data
Route::get('/chart', [TodoController::class, 'chart']);

Route::get('/todos/{id}', [TodoController::class, 'show']);
Route::put('/todos/{id}', [TodoController::class, 'update']);
Route::delete('/todos/{id}', [TodoController::class, 'destroy']);
