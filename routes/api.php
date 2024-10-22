<?php

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Api\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('book')->group(function () {
    Route::get('/', [BookController::class, 'index']);
    Route::post('/', [BookController::class, 'store']);
    Route::put('/', [BookController::class, 'update']);
    Route::delete('/', [BookController::class, 'destroy']);

    Route::get('/download', [BookController::class, 'download']);
});
