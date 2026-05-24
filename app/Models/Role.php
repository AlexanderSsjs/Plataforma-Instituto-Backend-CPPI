<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * 🌟 VITAL: Forzamos a Eloquent a usar tu tabla en español 'roles'.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
    ];

    /**
     * Relación Inversa: Un Rol puede ser asignado a muchos Usuarios (1:N).
     */
    public function usuarios(): HasMany
    {
        // Enlazamos con tu modelo User usando 'rol_id' como llave foránea
        return $this->hasMany(User::class, 'rol_id');
    }
}