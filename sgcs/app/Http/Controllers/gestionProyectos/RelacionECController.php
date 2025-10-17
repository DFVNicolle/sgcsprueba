<?php

namespace App\Http\Controllers\gestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\ElementoConfiguracion;
use App\Models\RelacionEC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RelacionECController extends Controller
{
    /**
     * Verifica que el usuario sea el creador del proyecto
     */
    private function verificarCreador(Proyecto $proyecto)
    {
        if ($proyecto->creado_por !== Auth::user()->id) {
            abort(403, 'No tienes permiso para gestionar las relaciones de este proyecto.');
        }
    }

    /**
     * Muestra el formulario para crear una relación
     */
    public function create(Proyecto $proyecto, ElementoConfiguracion $elemento)
    {
        $this->verificarCreador($proyecto);

        if ($elemento->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        // Obtener elementos disponibles para relacionar (excluyendo el actual)
        $elementosDisponibles = $proyecto->elementosConfiguracion()
            ->where('id', '!=', $elemento->id)
            ->orderBy('codigo_ec')
            ->get();

        return view('gestionProyectos.relaciones.create', compact('proyecto', 'elemento', 'elementosDisponibles'));
    }

    /**
     * Almacena una nueva relación
     */
    public function store(Request $request, Proyecto $proyecto, ElementoConfiguracion $elemento)
    {
        $this->verificarCreador($proyecto);

        if ($elemento->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        $validated = $request->validate([
            'hacia_ec' => 'required|exists:elementos_configuracion,id',
            'tipo_relacion' => 'required|in:DEPENDE_DE,DERIVADO_DE,REFERENCIA,REQUERIDO_POR',
            'nota' => 'nullable|string|max:500',
        ]);

        // Verificar que no se esté relacionando consigo mismo
        if ($validated['hacia_ec'] === $elemento->id) {
            return redirect()->back()
                ->withErrors(['hacia_ec' => 'No puedes relacionar un elemento consigo mismo.'])
                ->withInput();
        }

        // Verificar que la relación no exista ya
        $existeRelacion = RelacionEC::where('desde_ec', $elemento->id)
            ->where('hacia_ec', $validated['hacia_ec'])
            ->where('tipo_relacion', $validated['tipo_relacion'])
            ->exists();

        if ($existeRelacion) {
            return redirect()->back()
                ->withErrors(['hacia_ec' => 'Esta relación ya existe.'])
                ->withInput();
        }

        // Crear la relación
        $relacion = new RelacionEC();
        $relacion->id = (string) Str::uuid();
        $relacion->desde_ec = $elemento->id;
        $relacion->hacia_ec = $validated['hacia_ec'];
        $relacion->tipo_relacion = $validated['tipo_relacion'];
        $relacion->nota = $validated['nota'];
        $relacion->save();

        return redirect()
            ->route('proyectos.elementos.relaciones.index', [$proyecto, $elemento])
            ->with('success', 'Relación creada exitosamente.');
    }

    /**
     * Muestra las relaciones de un elemento
     */
    public function index(Proyecto $proyecto, ElementoConfiguracion $elemento)
    {
        $this->verificarCreador($proyecto);

        if ($elemento->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        // Relaciones que salen de este elemento
        $relacionesDesde = $elemento->relacionesDesde()
            ->with('elementoHacia')
            ->get();

        // Relaciones que apuntan a este elemento
        $relacionesHacia = $elemento->relacionesHacia()
            ->with('elementoDesde')
            ->get();

        return view('gestionProyectos.relaciones.index', compact(
            'proyecto',
            'elemento',
            'relacionesDesde',
            'relacionesHacia'
        ));
    }

    /**
     * Elimina una relación
     */
    public function destroy(Proyecto $proyecto, ElementoConfiguracion $elemento, RelacionEC $relacion)
    {
        $this->verificarCreador($proyecto);

        if ($elemento->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        // Verificar que la relación pertenezca al elemento
        if ($relacion->desde_ec !== $elemento->id && $relacion->hacia_ec !== $elemento->id) {
            abort(404);
        }

        $relacion->delete();

        return redirect()
            ->route('proyectos.elementos.relaciones.index', [$proyecto, $elemento])
            ->with('success', 'Relación eliminada exitosamente.');
    }
}
