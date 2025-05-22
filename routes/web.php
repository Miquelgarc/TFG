<?php

use App\Http\Controllers\AfiliatController;
use App\Http\Controllers\ReservesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AffiliateLinkController;
use App\Http\Controllers\HouseController;

use Database\Seeders\UsersTableSeeder;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');

    Route::get('/info-afiliat', [AfiliatController::class, 'index'])->name('infoAfiliat');

    Route::get('/afiliats', [UserController::class, 'indexAfiliats'])->name('afiliats');

    Route::post('/afiliats/{id}/cambiar-estat', [UserController::class, 'changeStatus']);
    Route::get('/afiliats/{user}/editar', [UserController::class, 'edit']);
    Route::delete('/afiliats/{user}', [UserController::class, 'destroy']);

    Route::get('/afiliats/comisions', [UserController::class, 'Comisions'])->name(name: 'comisions');

    Route::get('/afiliats/links', [UserController::class, 'Links'])->name('links');

    Route::get('/afiliats/links/export', [UserController::class, 'export'])->name('links.export');

    Route::get('/houses', [HouseController::class, 'indexProperties'])->name('houses');

    Route::get('/reserves', [ReservesController::class, 'index'])->name('reservations');
    
    Route::get('/reserva', [ReservesController::class, 'indexProperties'])->name('reservations.index');

    Route::post('/reserva', [ReservesController::class, 'store'])->name('reservations.store');

});


Route::get('/link/{code}', [AffiliateLinkController::class, 'redirect'])->name('affiliate.redirect');


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
