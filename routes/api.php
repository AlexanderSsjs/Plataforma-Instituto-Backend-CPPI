<?php

use App\Http\Controllers\Api\AuthController;
// 🏢 IMPORTACIÓN OBLIGATORIA: Apunta al nuevo controlador que creamos en la subcarpeta Api
use App\Http\Controllers\Api\AlumnoController; 
use Illuminate\Support\Facades\Route;

// 🔓 RUTAS PÚBLICAS: Cualquier usuario o invitado de React puede acceder a ellas
Route::post('/login', [AuthController::class, 'login']);


// 🔒 RUTAS PROTEGIDAS GLOBALMENTE: Solo accesibles si React envía un 'Bearer Token' válido
Route::middleware('auth:sanctum')->group(function () {
    
    // 🎯 REFUERZO CRÍTICO PARA F5: Devuelve los datos del usuario autenticado en tiempo real
    Route::get('/user', [AuthController::class, 'user']); 

    // Ruta para cerrar sesión de forma segura
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // 🏫 RECURSO ALUMNOS BLINDADO POR DENTRO
    // Esta única línea mapea los 5 métodos (index, store, show, update, destroy).
    // Como el controlador implementa 'HasMiddleware', Laravel ejecutará el filtro de roles automáticamente.
    Route::apiResource('/alumnos', AlumnoController::class);

    // 🧭 En el futuro, tus demás módulos (Cursos, Horarios, etc.) se agregarán igual de limpio aquí:
    // Route::apiResource('/cursos', CursoController::class);
});