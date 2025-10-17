<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodologiasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar metodologías
        $metodologias = [
            [
                'nombre' => 'Scrum',
                'tipo' => 'ágil',
                'descripcion' => 'Framework ágil basado en sprints, roles definidos y entregas iterativas'
            ],
            [
                'nombre' => 'Cascada',
                'tipo' => 'secuencial',
                'descripcion' => 'Metodología tradicional con fases secuenciales y entregables por etapa'
            ],
            [
                'nombre' => 'Kanban',
                'tipo' => 'ágil',
                'descripcion' => 'Sistema visual de gestión de flujo continuo con límites WIP'
            ],
        ];

        foreach ($metodologias as $metodologia) {
            DB::table('metodologias')->insertOrIgnore($metodologia);
        }

        // Obtener IDs de metodologías insertadas
        $scrum = DB::table('metodologias')->where('nombre', 'Scrum')->first();
        $cascada = DB::table('metodologias')->where('nombre', 'Cascada')->first();
        $kanban = DB::table('metodologias')->where('nombre', 'Kanban')->first();

        // Fases para SCRUM
        if ($scrum) {
            $fases_scrum = [
                ['id_metodologia' => $scrum->id_metodologia, 'nombre_fase' => 'Product Backlog', 'orden' => 1, 'descripcion' => 'Repositorio de historias de usuario pendientes'],
                ['id_metodologia' => $scrum->id_metodologia, 'nombre_fase' => 'Sprint Planning', 'orden' => 2, 'descripcion' => 'Planificación y selección de historias para el sprint'],
                ['id_metodologia' => $scrum->id_metodologia, 'nombre_fase' => 'In Progress', 'orden' => 3, 'descripcion' => 'Tareas en desarrollo activo durante el sprint'],
                ['id_metodologia' => $scrum->id_metodologia, 'nombre_fase' => 'In Review', 'orden' => 4, 'descripcion' => 'Revisión de código y validación de criterios de aceptación'],
                ['id_metodologia' => $scrum->id_metodologia, 'nombre_fase' => 'Done', 'orden' => 5, 'descripcion' => 'Historias completadas y aceptadas'],
            ];

            foreach ($fases_scrum as $fase) {
                DB::table('fases_metodologia')->insertOrIgnore($fase);
            }
        }

        // Fases para CASCADA
        if ($cascada) {
            $fases_cascada = [
                ['id_metodologia' => $cascada->id_metodologia, 'nombre_fase' => 'Requisitos', 'orden' => 1, 'descripcion' => 'Recolección y definición de requerimientos del sistema'],
                ['id_metodologia' => $cascada->id_metodologia, 'nombre_fase' => 'Análisis', 'orden' => 2, 'descripcion' => 'Análisis detallado de requisitos y especificaciones'],
                ['id_metodologia' => $cascada->id_metodologia, 'nombre_fase' => 'Diseño', 'orden' => 3, 'descripcion' => 'Diseño arquitectónico y detallado del sistema'],
                ['id_metodologia' => $cascada->id_metodologia, 'nombre_fase' => 'Implementación', 'orden' => 4, 'descripcion' => 'Codificación y desarrollo del sistema'],
                ['id_metodologia' => $cascada->id_metodologia, 'nombre_fase' => 'Pruebas', 'orden' => 5, 'descripcion' => 'Testing, validación y verificación del sistema'],
                ['id_metodologia' => $cascada->id_metodologia, 'nombre_fase' => 'Despliegue', 'orden' => 6, 'descripcion' => 'Implementación y puesta en producción'],
                ['id_metodologia' => $cascada->id_metodologia, 'nombre_fase' => 'Mantenimiento', 'orden' => 7, 'descripcion' => 'Soporte y mantenimiento post-despliegue'],
            ];

            foreach ($fases_cascada as $fase) {
                DB::table('fases_metodologia')->insertOrIgnore($fase);
            }
        }

        // Fases para KANBAN
        if ($kanban) {
            $fases_kanban = [
                ['id_metodologia' => $kanban->id_metodologia, 'nombre_fase' => 'Backlog', 'orden' => 1, 'descripcion' => 'Tareas pendientes por iniciar'],
                ['id_metodologia' => $kanban->id_metodologia, 'nombre_fase' => 'Selected for Dev', 'orden' => 2, 'descripcion' => 'Tareas seleccionadas para desarrollo'],
                ['id_metodologia' => $kanban->id_metodologia, 'nombre_fase' => 'In Progress', 'orden' => 3, 'descripcion' => 'Trabajo en curso'],
                ['id_metodologia' => $kanban->id_metodologia, 'nombre_fase' => 'Review', 'orden' => 4, 'descripcion' => 'En revisión de calidad'],
                ['id_metodologia' => $kanban->id_metodologia, 'nombre_fase' => 'Done', 'orden' => 5, 'descripcion' => 'Completado y entregado'],
            ];

            foreach ($fases_kanban as $fase) {
                DB::table('fases_metodologia')->insertOrIgnore($fase);
            }
        }
    }
}
