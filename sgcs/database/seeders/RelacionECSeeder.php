<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ElementoConfiguracion;
use App\Models\RelacionEC;

class RelacionECSeeder extends Seeder
{
    public function run(): void
    {
    $backend = ElementoConfiguracion::where('titulo', 'Backend')->first();
    $frontend = ElementoConfiguracion::where('titulo', 'Frontend')->first();
        if (!$backend || !$frontend) return;

        // Relacionar Backend y Frontend
        RelacionEC::create([
            'desde_ec' => $backend->id,
            'hacia_ec' => $frontend->id,
            'tipo_relacion' => 'DEPENDE_DE',
            'nota' => 'Frontend depende del Backend',
        ]);
    }
}
