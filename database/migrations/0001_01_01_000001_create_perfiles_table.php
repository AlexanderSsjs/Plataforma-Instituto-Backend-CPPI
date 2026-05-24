<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id(); // id SERIAL PRIMARY KEY
            
            // Relación 1:1 única con usuarios y borrado en cascada
            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->onDelete('cascade');
            
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('tipo_documento', 20); // 'DNI', 'CE', 'Pasaporte'
            $table->string('numero_documento', 30)->unique();
            $table->string('telefono', 20)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('genero', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->string('foto_perfil', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfiles');
    }
};