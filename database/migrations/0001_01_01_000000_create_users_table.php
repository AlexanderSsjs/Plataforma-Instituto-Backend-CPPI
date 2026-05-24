<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. TABLA DE CREDENCIALES Y ACCESOS (Modificada con tu diseño)
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id(); // id SERIAL PRIMARY KEY
            
            // Llave foránea hacia la tabla roles (Debe ejecutarse después de crear roles)
            $table->foreignId('rol_id')->constrained('roles'); 
            
            $table->string('email', 150)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->rememberToken(); // remember_token VARCHAR(100) NULL
            $table->string('estado', 20)->default('activo'); // 'activo', 'inactivo'
            $table->timestamps();
        });

        // 2. TABLA PARA RECUPERACIÓN DE CONTRASEÑAS (Ajustada a 'usuarios')
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 150)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. TABLA PARA SESIONES DE USUARIOS (Ajustada a 'usuarios')
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // Apunta a la tabla 'usuarios' que acabamos de definir arriba
            $table->foreignId('user_id')->nullable()->index()->constrained('usuarios')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // El orden de eliminación es inverso para no romper las restricciones
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
    }
};