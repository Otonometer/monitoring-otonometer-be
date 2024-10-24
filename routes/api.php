<?php

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('book')->group(function () {
    Route::get('/', [BookController::class, 'index']);
    Route::post('/', [BookController::class, 'store']);
    Route::put('/', [BookController::class, 'update']);
    Route::delete('/', [BookController::class, 'destroy']);

    Route::get('/download', [BookController::class, 'download']);
});

Route::prefix('notification')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/', [NotificationController::class, 'store']);
});
