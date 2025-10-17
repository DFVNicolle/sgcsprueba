<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Usuario;
use App\Models\Equipo;
use App\Models\MiembroEquipo;
use App\Models\Metodologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $usuarioId = Auth::user()->id;

        // 📊 INDICADORES GENERALES

        // 1. Proyectos que YO creé
        $proyectosCreados = Proyecto::where('creado_por', $usuarioId)->count();

        // 2. Proyectos donde SOY miembro (a través de equipos)
        $proyectosAsignados = Proyecto::whereHas('equipos.miembros', function ($query) use ($usuarioId) {
            $query->where('usuario_id', $usuarioId);
        })->count();

        // 3. Solicitudes de cambio pendientes (por ahora 0, implementaremos después)
        $cambiosPendientes = 0;

        // 4. Elementos de configuración (por ahora 0, implementaremos después)
        $elementosConfiguracion = 0;

        // 📁 PROYECTOS QUE YO CREÉ (máximo 5 recientes)
        $misProyectosCollection = Proyecto::with(['equipos' => function($query) {
                $query->withCount('miembros');
            }])
            ->where('creado_por', $usuarioId)
            ->orderBy('creado_en', 'desc')
            ->limit(5)
            ->get();

        // Resolver nombres de metodologías en batch para evitar N+1
        $metodologiaIds = $misProyectosCollection->pluck('id_metodologia')->filter()->unique()->values()->all();
        $metodologiasMap = Metodologia::whereIn('id_metodologia', $metodologiaIds)->get()->keyBy('id_metodologia')->map(function($m) { return $m->nombre; });

        $misProyectos = $misProyectosCollection->map(function($proyecto) use ($metodologiasMap) {
                // Calcular progreso ficticio basado en fecha (temporal)
                $diasDesdeCreacion = now()->diffInDays($proyecto->creado_en);
                $progreso = min(100, $diasDesdeCreacion * 5);
                $metNombre = $metodologiasMap[$proyecto->id_metodologia] ?? 'No especificada';

                return [
                    'id' => $proyecto->id,
                    'codigo' => $proyecto->codigo,
                    'nombre' => $proyecto->nombre,
                    'id_metodologia' => $proyecto->id_metodologia,
                    'metodologia' => $metNombre,
                    'total_equipos' => $proyecto->equipos->count(),
                    'total_miembros' => $proyecto->equipos->sum('miembros_count'),
                    'progreso' => $progreso,
                    'estado' => 'Activo', // temporal
                    'iniciales' => $this->getIniciales($proyecto->nombre),
                ];
            });

        // 👥 PROYECTOS DONDE SOY MIEMBRO (máximo 5 recientes)
        $proyectosParticipando = Proyecto::with(['equipos' => function($query) use ($usuarioId) {
                $query->whereHas('miembros', function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                })->with(['miembros' => function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                }]);
            }])
            ->whereHas('equipos.miembros', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->where('creado_por', '!=', $usuarioId) // Excluir proyectos que yo creé
            ->orderBy('creado_en', 'desc')
            ->limit(5)
            ->get();

        // Resolver metodologías para los proyectos donde participa el usuario
        $metodologiaIdsPart = $proyectosParticipando->pluck('id_metodologia')->filter()->unique()->values()->all();
        $metodologiasMapPart = Metodologia::whereIn('id_metodologia', $metodologiaIdsPart)->get()->keyBy('id_metodologia')->map(function($m) { return $m->nombre; });

        $proyectosParticipando = $proyectosParticipando->map(function($proyecto) use ($usuarioId, $metodologiasMapPart) {
                // Obtener mi rol en este proyecto
                $miEquipo = $proyecto->equipos->first();
                $miembro = $miEquipo->miembros->first();

                // Obtener el rol desde el pivot
                $rolId = $miembro->pivot->rol_id;
                $miRol = \App\Models\Rol::find($rolId)->nombre ?? 'Miembro';

                // Calcular progreso ficticio basado en fecha (temporal)
                $diasDesdeCreacion = now()->diffInDays($proyecto->creado_en);
                $progreso = min(100, $diasDesdeCreacion * 5);

                $metNombre = $metodologiasMapPart[$proyecto->id_metodologia] ?? 'No especificada';

                return [
                    'id' => $proyecto->id,
                    'codigo' => $proyecto->codigo,
                    'nombre' => $proyecto->nombre,
                    'id_metodologia' => $proyecto->id_metodologia,
                    'metodologia' => $metNombre,
                    'mi_rol' => $miRol,
                    'nombre_equipo' => $miEquipo->nombre ?? 'Sin equipo',
                    'progreso' => $progreso,
                    'estado' => 'Activo', // temporal
                    'iniciales' => $this->getIniciales($proyecto->nombre),
                ];
            });

        return view('dashboard', compact(
            'proyectosCreados',
            'proyectosAsignados',
            'cambiosPendientes',
            'elementosConfiguracion',
            'misProyectos',
            'proyectosParticipando'
        ));
    }

    /**
     * Generar iniciales del nombre del proyecto
     */
    private function getIniciales($nombre)
    {
        $palabras = explode(' ', $nombre);
        if (count($palabras) >= 2) {
            return strtoupper(substr($palabras[0], 0, 1) . substr($palabras[1], 0, 1));
        }
        return strtoupper(substr($nombre, 0, 2));
    }
}
