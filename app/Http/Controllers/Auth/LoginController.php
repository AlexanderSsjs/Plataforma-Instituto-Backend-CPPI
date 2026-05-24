<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /**
     * Muestra la vista del Login mediante Inertia (React).
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Maneja la petición de inicio de sesión.
     * Retorna un JSON limpio compatible con tu authService.ts en React.
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validamos los campos de entrada
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Intentamos autenticar en tu nueva tabla 'usuarios'
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 3. Obtenemos el usuario cargando sus nuevas relaciones (roles y perfiles)
            /** @var \App\Models\User $user */
            $user = Auth::user()->load(['rol', 'perfil']);

            // 4. Restricción de seguridad: Validamos si su cuenta está activa
            if ($user->estado !== 'activo') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'message' => 'Tu cuenta se encuentra inactiva. Contacta al soporte académico.'
                ], 401);
            }

            // 5. Retornamos la estructura exacta que tu interface 'LoginResponse' espera en React
            return response()->json([
                'usuario' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'rol' => $user->rol ? $user->rol->slug : 'student', // 'superuser', 'admin', etc.
                    'nombres' => $user->perfil ? $user->perfil->nombres : 'Usuario',
                    'apellidos' => $user->perfil ? $user->perfil->apellidos : 'Anonimo',
                    'foto_perfil' => $user->perfil ? $user->perfil->foto_perfil : null,
                    'estado' => $user->estado,
                ],
                // Si usas tokens tradicionales lo mandas aquí, sino viaja por la cookie de sesión nativa
                'access_token' => $request->session()->token() 
            ], 200);
        }

        // Si las credenciales fallan, disparamos un error 401 para que lo capture tu parseBackendError
        return response()->json([
            'message' => 'El correo electrónico o la contraseña son incorrectos.'
        ], 401);
    }

    /**
     * Cerrar sesión de forma segura limpiando cookies y estados del servidor.
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sesión cerrada con éxito.'], 200);
    }
}