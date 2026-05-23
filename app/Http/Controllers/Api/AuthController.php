<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{
    /**
     * Maneja la autenticación de usuarios vía API (Sanctum).
     */
    public function login(Request $request)
    {
        // 1. Validación estricta de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // 2. Intentar autenticar las credenciales directamente con Auth
            $credentials = $request->only('email', 'password');
            
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Credenciales incorrectas o el usuario no existe.'
                ], 401);
            }

            // 3. Si las credenciales son correctas, obtenemos el modelo del usuario
            $user = User::where('email', $request->email)->firstOrFail();

            // 4. Limpieza: Elimina tokens viejos para evitar acumular basura en la DB
            $user->tokens()->delete();

            // 5. Generación del Token de acceso para la sesión actual
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'token' => $token, // 🔒 CORREGIDO: Cambiado de 'access_token' a 'token' para alinearse con React
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role, // 🔒 REFUERZO: Enviamos el rol ('admin', 'teacher', 'student') para habilitar los guardianes de React
                ]
            ], 200);

        } catch (Exception $e) {
            // 6. En caso de fallo crítico, registramos el error internamente
            Log::error('Error crítico en el proceso de Login: ' . $e->getMessage());

            return response()->json([
                'status' => 'critical',
                'message' => 'Fallo de conexión en el servidor remoto.'
            ], 500);
        }
    }

    /**
     * 🎯 REFUERZO PARA RECARGAS (F5): Retorna el usuario autenticado.
     * Sanctum intercepta el Bearer Token enviado por React, valida la sesión
     * e inyecta al usuario correspondiente en la petición automáticamente.
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role, // Enviamos el rol para restablecer la barra lateral y los permisos dinámicos
            ], 200);

        } catch (Exception $e) {
            Log::error('Error al recuperar sesión activa en F5: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo validar la sesión activa.'
            ], 401);
        }
    }

    /**
     * Cierra la sesión activa revocando el token actual.
     */
    public function logout(Request $request)
    {
        try {
            // Elimina únicamente el token que se está usando en esta sesión
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Sesión cerrada correctamente'
            ], 200);
            
        } catch (Exception $e) {
            Log::error('Error al intentar cerrar sesión: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo procesar el cierre de sesión.'
            ], 500);
        }
    }
}