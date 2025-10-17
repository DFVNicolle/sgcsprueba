<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CronogramaTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scrumProject = DB::table('proyectos')->where('codigo', 'SCRUM-DEMO')->first();
        $cascadaProject = DB::table('proyectos')->where('codigo', 'CASCADA-DEMO')->first();
        if (!$scrumProject || !$cascadaProject) {
            $this->command->error('❌ Proyectos demo no encontrados');
            return;
        }

        // --- SCRUM TASKS ---
        $scrumFases = DB::table('fases_metodologia')->where('id_metodologia', $scrumProject->id_metodologia)->orderBy('orden')->get();
        $scrumECs = DB::table('elementos_configuracion')->where('proyecto_id', $scrumProject->id)->pluck('id')->toArray();
        $scrumTeam = DB::table('equipos')->where('proyecto_id', $scrumProject->id)->first();
        $scrumMiembros = DB::table('miembros_equipo')->where('equipo_id', $scrumTeam->id)->pluck('usuario_id')->toArray();

        DB::table('tareas_proyecto')->where('id_proyecto', $scrumProject->id)->delete();

        $scrumTasks = [
            ['nombre' => 'Sprint Planning', 'descripcion' => 'Planificación del sprint y selección de historias', 'story_points' => 3, 'horas_estimadas' => 8, 'prioridad' => 3, 'tipo_tarea' => 'Sprint Planning', 'dias_duracion' => 2],
            ['nombre' => 'Implementar API Auth', 'descripcion' => 'Desarrollar endpoints de autenticación', 'story_points' => 5, 'horas_estimadas' => 16, 'prioridad' => 3, 'tipo_tarea' => 'In Progress', 'dias_duracion' => 3],
            ['nombre' => 'Diseñar UI Login', 'descripcion' => 'Crear interfaz de usuario para login', 'story_points' => 4, 'horas_estimadas' => 10, 'prioridad' => 2, 'tipo_tarea' => 'In Progress', 'dias_duracion' => 2],
            ['nombre' => 'CRUD Pedidos', 'descripcion' => 'Desarrollar funcionalidad CRUD para pedidos', 'story_points' => 6, 'horas_estimadas' => 14, 'prioridad' => 2, 'tipo_tarea' => 'In Progress', 'dias_duracion' => 3],
            ['nombre' => 'Docs API', 'descripcion' => 'Documentar endpoints y uso de la API', 'story_points' => 2, 'horas_estimadas' => 6, 'prioridad' => 1, 'tipo_tarea' => 'In Review', 'dias_duracion' => 2],
            ['nombre' => 'Sprint Retrospective', 'descripcion' => 'Revisión y mejora continua del sprint', 'story_points' => 2, 'horas_estimadas' => 4, 'prioridad' => 1, 'tipo_tarea' => 'Done', 'dias_duracion' => 1],
        ];
        $scrumFecha = Carbon::now();
        foreach ($scrumFases as $idx => $fase) {
            if ($idx >= count($scrumTasks)) break;
            $tarea = $scrumTasks[$idx];
            $fechaInicioTarea = $scrumFecha->copy();
            $fechaFinTarea = $scrumFecha->copy()->addDays($tarea['dias_duracion']);
            DB::table('tareas_proyecto')->insert([
                'id_proyecto' => $scrumProject->id,
                'id_fase' => $fase->id_fase,
                'id_ec' => !empty($scrumECs) ? $scrumECs[array_rand($scrumECs)] : null,
                'nombre' => $tarea['nombre'],
                'descripcion' => $tarea['descripcion'],
                'story_points' => $tarea['story_points'],
                'horas_estimadas' => $tarea['horas_estimadas'],
                'responsable' => !empty($scrumMiembros) ? $scrumMiembros[array_rand($scrumMiembros)] : null,
                'estado' => 'pendiente',
                'prioridad' => $tarea['prioridad'],
                'fecha_inicio' => $fechaInicioTarea,
                'fecha_fin' => $fechaFinTarea,
                'sprint' => $tarea['tipo_tarea'],
                'notas' => 'Tarea Scrum generada automáticamente',
                'creado_en' => now(),
                'actualizado_en' => now(),
            ]);
            $scrumFecha = $fechaFinTarea->copy()->addDay();
        }

        // --- CASCADA TASKS ---
        $cascadaFases = DB::table('fases_metodologia')->where('id_metodologia', $cascadaProject->id_metodologia)->orderBy('orden')->get();
        $cascadaECs = DB::table('elementos_configuracion')->where('proyecto_id', $cascadaProject->id)->pluck('id')->toArray();
        $cascadaTeam = DB::table('equipos')->where('proyecto_id', $cascadaProject->id)->first();
        $cascadaMiembros = DB::table('miembros_equipo')->where('equipo_id', $cascadaTeam->id)->pluck('usuario_id')->toArray();

        DB::table('tareas_proyecto')->where('id_proyecto', $cascadaProject->id)->delete();

        $cascadaTasks = [
            ['nombre' => 'Reunión de requisitos', 'descripcion' => 'Recopilar requisitos funcionales y no funcionales', 'story_points' => 3, 'horas_estimadas' => 8, 'prioridad' => 3, 'tipo_tarea' => 'Requisitos', 'dias_duracion' => 2],
            ['nombre' => 'Diseño arquitectónico', 'descripcion' => 'Definir arquitectura y componentes', 'story_points' => 5, 'horas_estimadas' => 16, 'prioridad' => 3, 'tipo_tarea' => 'Diseño', 'dias_duracion' => 3],
            ['nombre' => 'Desarrollo módulo pedidos', 'descripcion' => 'Implementar módulo principal de pedidos', 'story_points' => 6, 'horas_estimadas' => 14, 'prioridad' => 2, 'tipo_tarea' => 'Implementación', 'dias_duracion' => 4],
            ['nombre' => 'Script BD pedidos', 'descripcion' => 'Crear script de base de datos para pedidos', 'story_points' => 2, 'horas_estimadas' => 6, 'prioridad' => 1, 'tipo_tarea' => 'Despliegue', 'dias_duracion' => 2],
            ['nombre' => 'Manual de usuario', 'descripcion' => 'Redactar guía para el usuario final', 'story_points' => 2, 'horas_estimadas' => 4, 'prioridad' => 1, 'tipo_tarea' => 'Mantenimiento', 'dias_duracion' => 1],
        ];
        $cascadaFecha = Carbon::now();
        foreach ($cascadaFases as $idx => $fase) {
            if ($idx >= count($cascadaTasks)) break;
            $tarea = $cascadaTasks[$idx];
            $fechaInicioTarea = $cascadaFecha->copy();
            $fechaFinTarea = $cascadaFecha->copy()->addDays($tarea['dias_duracion']);
            DB::table('tareas_proyecto')->insert([
                'id_proyecto' => $cascadaProject->id,
                'id_fase' => $fase->id_fase,
                'id_ec' => !empty($cascadaECs) ? $cascadaECs[array_rand($cascadaECs)] : null,
                'nombre' => $tarea['nombre'],
                'descripcion' => $tarea['descripcion'],
                'story_points' => $tarea['story_points'],
                'horas_estimadas' => $tarea['horas_estimadas'],
                'responsable' => !empty($cascadaMiembros) ? $cascadaMiembros[array_rand($cascadaMiembros)] : null,
                'estado' => 'pendiente',
                'prioridad' => $tarea['prioridad'],
                'fecha_inicio' => $fechaInicioTarea,
                'fecha_fin' => $fechaFinTarea,
                'sprint' => $tarea['tipo_tarea'],
                'notas' => 'Tarea Cascada generada automáticamente',
                'creado_en' => now(),
                'actualizado_en' => now(),
            ]);
            $cascadaFecha = $fechaFinTarea->copy()->addDay();
        }

        $this->command->info('✅ Tareas de prueba creadas para ambos proyectos demo');
    }
}
