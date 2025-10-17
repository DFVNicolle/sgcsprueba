<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Proyecto;
use App\Models\Equipo;
use App\Models\Usuario;

class EquipoSeeder extends Seeder
{
    public function run(): void
    {
        $proyecto2 = Proyecto::where('nombre', 'Proyectos Asignados')->first();
        $admin = Usuario::where('correo', 'admin@demo.com')->first();
        $lider = Usuario::where('correo', 'lider@demo.com')->first();
        if (!$proyecto2 || !$admin || !$lider) return;

        // Crear equipo "Equipo Alpha" con líder demo
        $equipoAlpha = Equipo::create([
            'id' => Str::uuid()->toString(),
            'nombre' => 'Equipo Alpha',
            'proyecto_id' => $proyecto2->id,
            'lider_id' => $lider->id,
        ]);

        // Crear equipo "Equipo Beta" y asignar admin como miembro
        $equipoBeta = Equipo::create([
            'id' => Str::uuid()->toString(),
            'nombre' => 'Equipo Beta',
            'proyecto_id' => $proyecto2->id,
            'lider_id' => $lider->id,
        ]);

        // Asignar admin como miembro de Equipo Beta con rol 'administrador'
        $rolAdmin = \DB::table('roles')->where('nombre', 'administrador')->first();
        if ($rolAdmin) {
            $equipoBeta->miembros()->attach($admin->id, ['rol_id' => $rolAdmin->id]);
        }
    }
}
