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

        // 2. Creamos el SuperUsuario por defecto en la tabla 'usuarios'
        $superUserEmail = 'superuser@instituto.com'; // Este será tu correo de acceso
        
        // Verificamos si ya existe para no duplicarlo si vuelves a correr el seeder
        $userExists = DB::table('usuarios')->where('email', $superUserEmail)->exists();

        if (!$userExists) {
            // Buscamos el ID numérico que la base de datos le asignó al slug 'superuser'
            $superUserRolId = DB::table('roles')->where('slug', 'superuser')->value('id');

            // Insertamos las credenciales de acceso en la tabla 'usuarios'
            $usuarioId = DB::table('usuarios')->insertGetId([
                'rol_id' => $superUserRolId,
                'email' => $superUserEmail,
                'password' => Hash::make('AdminSecret2026*'), // Tu contraseña de acceso
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Como separaste los datos, le creamos también su fila en la tabla 'perfiles'
            DB::table('perfiles')->insert([
                'usuario_id' => $usuarioId,
                'nombres' => 'Super',
                'apellidos' => 'Usuario Maestro',
                'tipo_documento' => 'DNI',
                'numero_documento' => '00000000', // Un número por defecto seguro
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}