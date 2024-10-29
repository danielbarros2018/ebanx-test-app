<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::post('/reset', function () {
    Cache::flush();
    return response()->json(0, Response::HTTP_OK);
});

Route::get('/balance', [BalanceController::class, 'getBalance']);

Route::post('/event', [EventController::class, 'postEvent']);
