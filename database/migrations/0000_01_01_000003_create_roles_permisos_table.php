<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles_permisos', function (Blueprint $table) {
            $table->id(); // id SERIAL PRIMARY KEY
            
            // Llaves foráneas con eliminación en cascada
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade');
            
            $table->timestamp('created_at')->useCurrent();

            // CONSTRAINT uq_rol_permiso UNIQUE (rol_id, permiso_id)
            $table->unique(['rol_id', 'permiso_id'], 'uq_rol_permiso');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles_permisos');
    }
};