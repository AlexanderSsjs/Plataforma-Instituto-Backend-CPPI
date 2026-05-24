<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id(); // id SERIAL PRIMARY KEY
            $table->string('nombre', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->string('modulo', 50);
            $table->text('descripcion')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Solo created_at como tu SQL
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};