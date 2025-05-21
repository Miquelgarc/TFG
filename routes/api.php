<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\ReservationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth', 'verified'])->group(function () {
        Route::post('/reservations', [ReservationController::class, 'store']);
});




Route::get('/reservations', [ReservationController::class, 'index']);
