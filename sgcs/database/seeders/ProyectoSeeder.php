<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyecto;
use App\Models\Usuario;

class ProyectoSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar el usuario admin demo
        $usuario = Usuario::where('correo', 'admin@demo.com')->first();
        if (!$usuario) return;

        // Crear proyecto demo
        $proyecto = Proyecto::create([
            'nombre' => 'Proyecto Demo',
            'descripcion' => 'Proyecto de ejemplo para pruebas',
            'creado_por' => $usuario->id,
        ]);

        // Buscar usuario líder demo
        $lider = \App\Models\Usuario::where('correo', 'lider@demo.com')->first();
        // Crear proyecto adicional asignado al usuario admin pero con líder distinto
        $proyecto2 = Proyecto::create([
            'nombre' => 'Proyectos Asignados',
            'descripcion' => 'Proyecto para pruebas de asignación',
            'creado_por' => $lider ? $lider->id : $usuario->id,
        ]);
    }
}
