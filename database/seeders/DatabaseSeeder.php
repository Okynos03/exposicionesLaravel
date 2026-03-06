<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Maestro;
use App\Models\Alumno;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usa 'nombre_rol' porque así lo definiste en tu modelo Role
        Role::create(['nombre_rol' => 'Maestro']);
        Role::create(['nombre_rol' => 'Alumno']);

        // 2. Creamos un Maestro de prueba
        $userMaestro = User::create([
            'nombre' => 'Profe Troncoso',
            'email' => 'maestro@test.com',
            'password' => Hash::make('secret123'),
            'id_rol' => 1
        ]);

        Maestro::create([
            'id_usuario' => $userMaestro->id_usuario
        ]);

        // 3. Creamos un Alumno de prueba
        $userAlumno = User::create([
            'nombre' => 'Juanito Pérez',
            'email' => 'alumno@test.com',
            'password' => Hash::make('secret123'),
            'id_rol' => 2
        ]);

        Alumno::create([
            'id_usuario' => $userAlumno->id_usuario,
            'num_ctrl' => '22030128'
        ]);

        $this->command->info('Base de datos poblada con éxito: maestro@test.com y alumno@test.com');
    }
}
