<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Agent\ChatbotController;
use App\Http\Controllers\Voice\TranscripcionController;

use App\Http\Controllers\Api\AlumnoController; 
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/chat', [ChatbotController::class, 'procesarMensaje']);
Route::post('/transcribe', [TranscripcionController::class, 'procesarAudio']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']); 
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/alumnos', AlumnoController::class);
});