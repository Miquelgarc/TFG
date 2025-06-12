<?php

use App\Http\Controllers\AfiliatController;
use App\Http\Controllers\ReservesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AffiliateLinkController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\InvoiceController;

use Database\Seeders\UsersTableSeeder;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');
Route::get('/pendingUser', function () {
    return Inertia::render('auth/PendingUser');
})->name('pendingUser');
Route::get('/rejectedUser', function () {
    return Inertia::render('auth/RejectedUser');
})->name('rejectedUser');
Route::get('/property/{id}', [PropertyController::class, 'show'])->name('property.show');

Route::post('/reserva', [ReservesController::class, 'store'])->name('reservations.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/info-afiliat', [AfiliatController::class, 'index'])->name('infoAfiliat');

    Route::get('/afiliats', [UserController::class, 'indexAfiliats'])->name('afiliats');

    Route::post('/afiliats/{id}/cambiar-estat', [UserController::class, 'changeStatus']);
    Route::get('/afiliats/{user}/editar', [UserController::class, 'edit']);
    Route::delete('/afiliats/{user}', [UserController::class, 'destroy']);

    Route::get('/afiliats/comisions', [UserController::class, 'Comisions'])->name(name: 'comisions');

    Route::get('/afiliats/comisions/export', [UserController::class, 'exportComisions'])->name('comisions.export');
    Route::get('/afiliats/links', [UserController::class, 'Links'])->name('links');
    Route::get('/afiliats/links/create', [UserController::class, 'createLink'])->name('links.create');
    Route::post('/afiliats/links', [UserController::class, 'storeLink'])->name('links.store');
    Route::get('/afiliats/links/export', [UserController::class, 'exportLink'])->name('links.export');

    Route::get('/houses', [HouseController::class, 'indexProperties'])->name('houses');

    Route::get('/reserves', [ReservesController::class, 'index'])->name('reservations');

    Route::get('/reserva', [ReservesController::class, 'indexProperties'])->name('reservations.index');

    Route::get('/comisions/{commission}/invoice', [UserController::class, 'downloadInvoice'])
        ->name('comisions.invoice')
        ->middleware(['auth']);

    Route::get('/facturas/{invoice}', [InvoiceController::class, 'show'])
        ->name('invoices.show');
    Route::post('/admin/generar-facturas', function () {
        Artisan::call('invoices:generate');
        return back()->with('success', 'Facturas generadas correctamente.');
    })->name('invoices.generate');

});


Route::get('/link/{code}', [AffiliateLinkController::class, 'redirect'])->name('affiliate.redirect');


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
