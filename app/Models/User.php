<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * 🌟 VITAL: Obliga al modelo a conectarse a tu nueva tabla en español.
     * Esto solucionará el error SQLSTATE[42S02] (Table 'ccip_db.users' doesn't exist).
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * Atributos asignables en masa ajustados a la nueva estructura.
     * Eliminamos 'name' y 'role' (texto) ya que ahora están normalizados.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rol_id', // Llave foránea numérica hacia la tabla roles
        'email',
        'password',
        'estado', // 'activo' o 'inactivo'
    ];

    /**
     * Atributos ocultos para la serialización (no se envían en las respuestas JSON).
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts de atributos automatizados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    //  RELACIONES DE ELOQUENT (Para enlazar tu ecosistema RBAC)
    // =========================================================================

    /**
     * Relación: Muchos Usuarios pertenecen a un único Rol (N:1).
     */
    public function rol(): BelongsTo
    {
        // Vincula de forma nativa a través del campo 'rol_id'
        return $this->belongsTo(Role::class, 'rol_id');
    }

    /**
     * Relación: Un Usuario tiene un Perfil único de datos personales (1:1).
     */
    public function perfil(): HasOne
    {
        // Enlaza con la tabla perfiles usando 'usuario_id' como llave foránea
        return $this->hasOne(Perfil::class, 'usuario_id');
    }

    // =========================================================================
    //  MÉTODOS DE AYUDA DE IDENTIDAD (Para tus controladores y políticas)
    // =========================================================================

    /**
     * Verifica si el usuario tiene una etiqueta de rol específica (por su slug).
     */
    public function hasRole(string $slug): bool
    {
        return $this->rol && $this->rol->slug === $slug;
    }

    /**
     * Atajos rápidos booleanos de control de flujo
     */
    public function isSuperUser(): bool
    {
        return $this->hasRole('superuser');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }
}