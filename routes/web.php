<?php
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'create'])->name('login');
Route::post('login', [LoginController::class, 'store']);
Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

// Ruta protegida (solo accesible si estás logueado)
Route::middleware('auth')->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');