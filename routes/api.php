<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// 🔓 RUTAS PÚBLICAS: Cualquier usuario o invitado de React puede acceder a ellas
Route::post('/login', [AuthController::class, 'login']);


// 🔒 RUTAS PROTEGIDAS: Solo accesibles si React envía el 'Bearer Token' en las cabeceras (Headers)
Route::middleware('auth:sanctum')->group(function () {
    
    // 🎯 REFUERZO CRÍTICO PARA F5: Devuelve los datos del usuario autenticado en tiempo real
    Route::get('/user', [AuthController::class, 'user']); // O resolverlo directamente con un callback intermedio si lo prefieres

    // Ruta para cerrar sesión de forma segura
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Aquí puedes agregar más rutas del LMS en el futuro, por ejemplo:
    // Route::apiResource('/courses', CourseController::class);
});