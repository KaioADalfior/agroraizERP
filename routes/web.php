<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PainelController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/painel');

/*
|--------------------------------------------------------------------------
| Acesso (somente visitantes)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Área logada
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/painel', PainelController::class)->name('painel');
    Route::resource('clientes', ClienteController::class)->except('show');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
