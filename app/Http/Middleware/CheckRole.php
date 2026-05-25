<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Maneja una petición entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int|string  ...$roles Lista de rol_id permitidos (ej: 1, 2)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verificar si el usuario está autenticado en la sesión actual
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No autenticado.'
            ], 401);
        }

        // 2. Comprobar si el rol_id del usuario está dentro de los roles autorizados para la ruta
        // Convertimos a int para asegurar una comparación estricta y segura
        if (!in_array((int)$user->rol_id, array_map('intval', $roles))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acceso denegado. No tienes los permisos requeridos para este recurso.'
            ], 403); // 🔒 403 Forbidden: Blindaje total
        }

        return $next($request);
    }
}