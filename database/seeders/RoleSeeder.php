<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Insertamos las "etiquetas" de los roles en tu tabla 'roles'
        $roles = [
            ['nombre' => 'Super Administrador', 'slug' => 'superuser', 'descripcion' => 'Control Total del Sistema'],
            ['nombre' => 'Administrador', 'slug' => 'admin', 'descripcion' => 'Administrador del sistema'],
            ['nombre' => 'Secretario/a', 'slug' => 'secretary', 'descripcion' => 'Gestión Académica y Matrículas'],
            ['nombre' => 'Docente', 'slug' => 'teacher', 'descripcion' => 'Docente / Instructor'],
            ['nombre' => 'Estudiante', 'slug' => 'student', 'descripcion' => 'Alumno / Estudiante'],
        ];

        // Recorremos e insertamos evitando duplicar por el slug único
        foreach ($roles ?? [] as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']], 
                $role
            );
        }


        // --- 👑 USUARIO 1: SUPERUSUARIO ---
        $superUserEmail = 'superuser@instituto.com';
        if (!DB::table('usuarios')->where('email', $superUserEmail)->exists()) {
            $superUserRolId = DB::table('roles')->where('slug', 'superuser')->value('id');

            $usuarioId = DB::table('usuarios')->insertGetId([
                'rol_id' => $superUserRolId,
                'email' => $superUserEmail,
                'password' => Hash::make('AdminSecret2026*'),
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('perfiles')->insert([
                'usuario_id' => $usuarioId,
                'nombres' => 'Super',
                'apellidos' => 'Usuario Maestro',
                'tipo_documento' => 'DNI',
                'numero_documento' => '00000000',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // --- 📝 USUARIO 2: SECRETARIO ---
        $secretaryEmail = 'secretaria@instituto.com';
        if (!DB::table('usuarios')->where('email', $secretaryEmail)->exists()) {
            $secretaryRolId = DB::table('roles')->where('slug', 'secretary')->value('id');

            $secretaryId = DB::table('usuarios')->insertGetId([
                'rol_id' => $secretaryRolId,
                'email' => $secretaryEmail,
                'password' => Hash::make('Secretary2026*'), // Contraseña segura
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('perfiles')->insert([
                'usuario_id' => $secretaryId,
                'nombres' => 'Ana Maria',
                'apellidos' => 'Palacios Quiroz',
                'tipo_documento' => 'DNI',
                'numero_documento' => '77777777',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $studentEmail = 'alumno@instituto.com';
        if (!DB::table('usuarios')->where('email', $studentEmail)->exists()) {
            $studentRolId = DB::table('roles')->where('slug', 'student')->value('id');

            $studentId = DB::table('usuarios')->insertGetId([
                'rol_id' => $studentRolId,
                'email' => $studentEmail,
                'password' => Hash::make('Alumno2026*'), 
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('perfiles')->insert([
                'usuario_id' => $studentId,
                'nombres' => 'Alexander Bryan',
                'apellidos' => 'Piélago Quiroz',
                'tipo_documento' => 'DNI',
                'numero_documento' => '99999999',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}