<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Proyecto;
use App\Models\Rol;

class UsuariosRolesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Usuario::where('correo', 'admin@demo.com')->first();
        $proyecto2 = Proyecto::where('nombre', 'Proyectos Asignados')->first();
        $rolAdmin = DB::table('roles')->where('nombre', 'administrador')->first();
        if ($admin && $proyecto2 && $rolAdmin) {
            DB::table('usuarios_roles')->insertOrIgnore([
                'usuario_id' => $admin->id,
                'rol_id' => $rolAdmin->id,
                'proyecto_id' => $proyecto2->id,
            ]);
        }
    }
}
