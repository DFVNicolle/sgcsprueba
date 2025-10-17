<?php

namespace App\Http\Controllers\GestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\FaseMetodologia;
use App\Models\ElementoConfiguracion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TareaProyectoController extends Controller
{
    /**
     * Verifica que el usuario sea el creador del proyecto
     */
    private function verificarCreador(Proyecto $proyecto)
    {
        if ($proyecto->creado_por !== Auth::user()->id) {
            abort(403, 'Solo el creador del proyecto puede gestionar el cronograma.');
        }
    }

    /**
     * Muestra el tablero Kanban del proyecto
     */
    public function index(Proyecto $proyecto)
    {
        $this->verificarCreador($proyecto);

        // Obtener metodología y sus fases
        $metodologia = $proyecto->metodologia;
        $fases = FaseMetodologia::where('id_metodologia', $metodologia->id_metodologia)
            ->orderBy('orden')
            ->get();

        // Obtener todas las tareas con sus relaciones
        $tareas = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->with(['fase', 'elementoConfiguracion', 'responsableUsuario'])
            ->orderBy('prioridad', 'desc')
            ->orderBy('creado_en', 'desc')
            ->get()
            ->groupBy('id_fase');

        // Obtener elementos de configuración disponibles
        $elementosConfiguracion = $proyecto->elementosConfiguracion()
            ->orderBy('codigo_ec')
            ->get();

        // Obtener miembros del equipo para asignar como responsables
        $miembrosEquipo = collect();
        foreach ($proyecto->equipos as $equipo) {
            $miembrosEquipo = $miembrosEquipo->merge($equipo->miembros);
        }
        $miembrosEquipo = $miembrosEquipo->unique('id');

        return view('gestionProyectos.tareas.index', compact(
            'proyecto',
            'metodologia',
            'fases',
            'tareas',
            'elementosConfiguracion',
            'miembrosEquipo'
        ));
    }

    /**
     * Almacena una nueva tarea
     */
    public function store(Request $request, Proyecto $proyecto)
    {
        $this->verificarCreador($proyecto);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'id_fase' => 'required|exists:fases_metodologia,id_fase',
            'id_ec' => 'nullable|exists:elementos_configuracion,id',
            'responsable' => 'nullable|exists:usuarios,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'nullable|string|max:50',
            'story_points' => 'nullable|integer|min:0',
            'horas_estimadas' => 'nullable|numeric|min:0',
            'prioridad' => 'nullable|integer',
            'sprint' => 'nullable|string|max:50',
            'criterios_aceptacion' => 'nullable|array',
            'notas' => 'nullable|string',
        ]);

        $validated['id_proyecto'] = $proyecto->id;

        // Si no se especifica estado, usar el nombre de la fase como estado inicial
        if (empty($validated['estado'])) {
            $fase = FaseMetodologia::find($validated['id_fase']);
            $validated['estado'] = $fase->nombre_fase;
        }

        $tarea = TareaProyecto::create($validated);

        return redirect()
            ->route('proyectos.tareas.index', $proyecto)
            ->with('success', 'Tarea creada exitosamente.');
    }

    /**
     * Actualiza una tarea existente
     */
    public function update(Request $request, Proyecto $proyecto, TareaProyecto $tarea)
    {
        $this->verificarCreador($proyecto);

        if ($tarea->id_proyecto !== $proyecto->id) {
            abort(404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'id_fase' => 'required|exists:fases_metodologia,id_fase',
            'id_ec' => 'nullable|exists:elementos_configuracion,id',
            'responsable' => 'nullable|exists:usuarios,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'nullable|string|max:50',
            'story_points' => 'nullable|integer|min:0',
            'horas_estimadas' => 'nullable|numeric|min:0',
            'prioridad' => 'nullable|integer',
            'sprint' => 'nullable|string|max:50',
            'criterios_aceptacion' => 'nullable|array',
            'notas' => 'nullable|string',
        ]);

        // Detectar cambio de estado a revisión
        $estadoAnterior = $tarea->estado;
        $estadoNuevo = $validated['estado'] ?? $tarea->estado;

        $tarea->update($validated);

        // Si la tarea pasa a revisión Y tiene un EC asociado, cambiar estado del EC
        if ($this->esEstadoRevision($estadoNuevo) && !$this->esEstadoRevision($estadoAnterior)) {
            if ($tarea->id_ec) {
                $ec = ElementoConfiguracion::find($tarea->id_ec);
                if ($ec && $ec->estado !== 'EN_REVISION') {
                    $ec->estado = 'EN_REVISION';
                    $ec->save();
                }
            }
        }

        return redirect()
            ->route('proyectos.tareas.index', $proyecto)
            ->with('success', 'Tarea actualizada exitosamente.');
    }

    /**
     * Cambia la fase de una tarea (usado para drag & drop)
     */
    public function cambiarFase(Request $request, Proyecto $proyecto, TareaProyecto $tarea)
    {
        $this->verificarCreador($proyecto);

        if ($tarea->id_proyecto !== $proyecto->id) {
            return response()->json(['error' => 'Tarea no pertenece a este proyecto'], 404);
        }

        $validated = $request->validate([
            'id_fase' => 'required|exists:fases_metodologia,id_fase',
        ]);

        $estadoAnterior = $tarea->estado;
        $fase = FaseMetodologia::find($validated['id_fase']);

        $tarea->id_fase = $validated['id_fase'];
        $tarea->estado = $fase->nombre_fase; // Sincronizar estado con nombre de fase
        $tarea->save();

        // Verificar si el nuevo estado es "revisión" y cambiar EC si corresponde
        if ($this->esEstadoRevision($fase->nombre_fase) && !$this->esEstadoRevision($estadoAnterior)) {
            if ($tarea->id_ec) {
                $ec = ElementoConfiguracion::find($tarea->id_ec);
                if ($ec && $ec->estado !== 'EN_REVISION') {
                    $ec->estado = 'EN_REVISION';
                    $ec->save();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tarea movida exitosamente',
            'tarea' => $tarea->load(['fase', 'elementoConfiguracion', 'responsableUsuario'])
        ]);
    }

    /**
     * Elimina una tarea
     */
    public function destroy(Proyecto $proyecto, TareaProyecto $tarea)
    {
        $this->verificarCreador($proyecto);

        if ($tarea->id_proyecto !== $proyecto->id) {
            abort(404);
        }

        $tarea->delete();

        return redirect()
            ->route('proyectos.tareas.index', $proyecto)
            ->with('success', 'Tarea eliminada exitosamente.');
    }

    /**
     * Obtiene los datos de una tarea específica (para modal)
     */
    public function show(Proyecto $proyecto, TareaProyecto $tarea)
    {
        $this->verificarCreador($proyecto);

        if ($tarea->id_proyecto !== $proyecto->id) {
            return response()->json(['error' => 'Tarea no encontrada'], 404);
        }

        $tarea->load(['fase', 'elementoConfiguracion', 'responsableUsuario']);

        return response()->json($tarea);
    }

    /**
     * Verifica si un estado corresponde a "revisión"
     */
    private function esEstadoRevision($estado): bool
    {
        $estadosRevision = ['EN_REVISION', 'In Review', 'Review', 'REVISION'];
        return in_array($estado, $estadosRevision);
    }
}
