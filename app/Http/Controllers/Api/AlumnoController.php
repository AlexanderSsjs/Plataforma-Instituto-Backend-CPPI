<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// 🔒 Importaciones obligatorias para el estándar de seguridad de Laravel 11
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AlumnoController extends Controller implements HasMiddleware
{
    /**
     * 🔐 Definición de los Middlewares del Controlador.
     * Bloquea el acceso al alumno (rol 5) y permite la gestión a los roles 1, 2, 3 y 4.
     */
    public static function middleware(): array
    {
        return [
            // 🛡️ Todos los métodos de este recurso quedan blindados para personal y docentes
            new Middleware('role:1,2,3,4'),

            // 🛡️ Regla quirúrgica opcional: si en el futuro quieres que solo SuperAdmin(1)
            // o Admin(2) eliminen alumnos, puedes descomentar la siguiente línea:
            // new Middleware('role:1,2', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     * (Usado por React para pintar la tabla general de alumnos)
     */
    public function index()
    {
        // Aquí irá tu lógica de Eloquent (ej: Alumno::with('perfil')->get())
        return response()->json([
            'status' => 'success',
            'message' => 'Lista completa de alumnos recuperada con éxito.'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * (Procesa el formulario de matrícula enviado desde React)
     */
    public function store(Request $request)
    {
        // Aquí irá tu validación y la inserción en las tablas 'usuarios' y 'perfiles'
        return response()->json([
            'status' => 'success',
            'message' => 'Alumno matriculado y registrado en el sistema correctamente.'
        ]);
    }

    /**
     * Display the specified resource.
     * (Trae la ficha o el expediente de un solo alumno)
     */
    public function show(int $id)
    {
        return response()->json([
            'status' => 'success',
            'message' => "Expediente del alumno con ID {$id} extraído correctamente."
        ]);
    }

    /**
     * Update the specified resource in storage.
     * (Modifica los datos personales o académicos del alumno)
     */
    public function update(Request $request, int $id)
    {
        return response()->json([
            'status' => 'success',
            'message' => "Datos del alumno con ID {$id} actualizados en la base de datos."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * (Da de baja al alumno del instituto)
     */
    public function destroy(int $id)
    {
        return response()->json([
            'status' => 'success',
            'message' => "Alumno con ID {$id} dado de baja correctamente."
        ]);
    }
}