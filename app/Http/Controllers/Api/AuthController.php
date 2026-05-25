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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $credentials = $request->only('email', 'password');
            
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Credenciales incorrectas o el usuario no existe.'
                ], 401);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            // Limpieza de tokens antiguos
            $user->tokens()->delete();

            // Generación del Token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'token' => $token, 
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'rol_id' => $user->rol_id, // 🔒 CORREGIDO: Campo real de tu tabla
                    'estado' => $user->estado, // 🔒 CORREGIDO: Campo real de tu tabla
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error('Error crítico en el proceso de Login: ' . $e->getMessage());
            return response()->json([
                'status' => 'critical',
                'message' => 'Fallo de conexión en el servidor remoto.'
            ], 500);
        }
    }

    /**
     * Retorna el usuario autenticado (Soporte F5).
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'id' => $user->id,
                'email' => $user->email,
                'rol_id' => $user->rol_id, // 🔒 CORREGIDO
                'estado' => $user->estado, // 🔒 CORREGIDO
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