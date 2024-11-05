<?php

use App\Http\Controllers\Api\ServerStatusController;
use Illuminate\Support\Facades\Route;

Route::prefix('server-status')->group(function () {
    Route::get('/', [ServerStatusController::class, 'index']);
});
