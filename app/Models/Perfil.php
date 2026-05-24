<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perfil extends Model
{
    /**
     * 🌟 VITAL: Forzamos a Eloquent a usar tu tabla en español 'perfiles'.
     *
     * @var string
     */
    protected $table = 'perfiles';

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'usuario_id',
        'nombres',
        'apellidos',
        'tipo_documento',
        'numero_documento',
        'foto_perfil',
    ];

    /**
     * Relación: Este Perfil pertenece de forma exclusiva a un Usuario (1:1).
     */
    public function usuario(): BelongsTo
    {
        // Enlazamos de vuelta con la tabla 'usuarios' mediante 'usuario_id'
        return $this->belongsTo(User::class, 'usuario_id');
    }
}