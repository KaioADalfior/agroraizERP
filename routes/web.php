<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CulturaController;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\PropriedadeController;
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
    Route::resource('clientes', ClienteController::class);
    Route::post('/clientes/{cliente}/propriedades', [PropriedadeController::class, 'store'])->name('propriedades.store');
    Route::delete('/propriedades/{propriedade}', [PropriedadeController::class, 'destroy'])->name('propriedades.destroy');
    Route::get('/banco-de-dados', [CulturaController::class, 'index'])->name('culturas.index');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
