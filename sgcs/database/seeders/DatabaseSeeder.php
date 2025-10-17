<?php

namespace Database\Seeders;

use App\Models\Usuario;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            UsuarioSeeder::class,
            ProyectoSeeder::class,
            UsuariosRolesSeeder::class,
            EquipoSeeder::class,
            ElementoConfiguracionSeeder::class,
            RelacionECSeeder::class,
        ]);

        Usuario::factory()->create([
            'nombre_completo' => 'Roberto Silva Mendoza',
            'correo' => 'roberto.silva@sgcs.com',
            'activo' => true,
        ]);

        Usuario::factory()->create([
            'nombre_completo' => 'Sofía González Díaz',
            'correo' => 'sofia.gonzalez@sgcs.com',
            'activo' => true,
        ]);

        Usuario::factory()->create([
            'nombre_completo' => 'Diego Vargas Moreno',
            'correo' => 'diego.vargas@sgcs.com',
            'activo' => true,
        ]);

        $this->command->info('✅ Usuarios de prueba creados exitosamente!');
    }
}
