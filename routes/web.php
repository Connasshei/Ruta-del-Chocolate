<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\EventoPublicoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InscripcionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    Route::get('/eventos', [EventoPublicoController::class, 'index'])->name('eventos.index');
    Route::get('/eventos/{evento}', [EventoPublicoController::class, 'show'])->name('eventos.show');
    Route::post('/eventos/{evento}/inscripciones', [InscripcionController::class, 'store'])
        ->name('inscripciones.store');
    Route::delete('/inscripciones/{inscripcion}', [InscripcionController::class, 'destroy'])
        ->name('inscripciones.destroy');

    Route::middleware('role:guia|admin')->prefix('guias')->name('guias.')->group(function () {
        Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
        Route::get('/eventos/create', [EventoController::class, 'create'])->name('eventos.create');
        Route::post('/eventos', [EventoController::class, 'store'])->name('eventos.store');
        Route::get('/eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');
        Route::get('/eventos/{evento}/edit', [EventoController::class, 'edit'])->name('eventos.edit');
        Route::put('/eventos/{evento}', [EventoController::class, 'update'])->name('eventos.update');
        Route::patch('/eventos/{evento}/estado', [EventoController::class, 'cambiarEstado'])->name('eventos.estado');
        Route::delete('/eventos/{evento}', [EventoController::class, 'destroy'])->name('eventos.destroy');
        Route::patch('/inscripciones/{inscripcion}', [InscripcionController::class, 'actualizarEstado'])
            ->name('inscripciones.estado');
    });
});
