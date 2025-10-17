<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario líder demo
        Usuario::firstOrCreate([
            'correo' => 'lider@demo.com',
        ], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Líder Demo',
            'contrasena_hash' => Hash::make('lider123'),
            'correo_verificado_en' => now(),
        ]);

        Usuario::firstOrCreate([
            'correo' => 'admin@demo.com',
        ], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Admin Demo',
            'contrasena_hash' => Hash::make('admin123'),
            'correo_verificado_en' => now(),
            // 'google2fa_secret' => null, // Puedes agregar una clave si quieres probar 2FA
        ]);
    }
}
