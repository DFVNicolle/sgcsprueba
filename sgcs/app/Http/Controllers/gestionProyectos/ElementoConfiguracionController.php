<?php

namespace App\Http\Controllers\gestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\ElementoConfiguracion;
use App\Models\VersionEC;
use App\Models\CommitRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class ElementoConfiguracionController extends Controller
{
    /**
     * Muestra la vista del grafo de trazabilidad EC
     */
    public function verGrafo(Proyecto $proyecto)
    {
        $this->verificarCreador($proyecto);
        return view('gestionProyectos.elementos.grafo', compact('proyecto'));
    }
    /**
     * Devuelve los elementos de configuración y sus relaciones en formato grafo (nodes/edges)
     */
    public function grafo(Proyecto $proyecto)
    {
        $elementos = $proyecto->elementosConfiguracion()->get(['id', 'titulo', 'tipo']);
        $relaciones = \App\Models\RelacionEC::whereIn('desde_ec', $elementos->pluck('id'))
            ->whereIn('hacia_ec', $elementos->pluck('id'))
            ->get(['desde_ec', 'hacia_ec', 'tipo_relacion', 'nota']);

        $nodes = $elementos->map(function($e) {
            return [
                'id' => $e->id,
                'label' => $e->titulo,
                'group' => $e->tipo,
            ];
        });
        $edges = $relaciones->map(function($r) {
            return [
                'from' => $r->desde_ec,
                'to' => $r->hacia_ec,
                'label' => $r->tipo_relacion,
                'title' => $r->nota,
            ];
        });

        return response()->json([
            'nodes' => $nodes,
            'edges' => $edges,
        ]);
    }
    /**
     * Verifica que el usuario sea el creador del proyecto
     */
    private function verificarCreador(Proyecto $proyecto)
    {
        if ($proyecto->creado_por !== Auth::user()->id) {
            abort(403, 'Solo el creador del proyecto puede gestionar elementos de configuración.');
        }
    }

    /**
     * Muestra listado de elementos de configuración del proyecto
     */
    public function index(Proyecto $proyecto)
    {
        $this->verificarCreador($proyecto);

        $elementos = $proyecto->elementosConfiguracion()
            ->with(['creador', 'versionActual.commit', 'versiones'])
            ->orderBy('creado_en', 'desc')
            ->get();

        return view('gestionProyectos.elementos.index', compact('proyecto', 'elementos'));
    }

    /**
     * Muestra formulario de creación
     */
    public function create(Proyecto $proyecto)
    {
        $this->verificarCreador($proyecto);

        // Obtener todos los miembros de todos los equipos del proyecto
        $miembrosEquipo = collect();
        foreach ($proyecto->equipos as $equipo) {
            $miembrosEquipo = $miembrosEquipo->merge($equipo->miembros);
        }
        // Eliminar duplicados si un usuario está en varios equipos
        $miembrosEquipo = $miembrosEquipo->unique('id');

        return view('gestionProyectos.elementos.create', compact('proyecto', 'miembrosEquipo'));
    }

    /**
     * Almacena un nuevo elemento de configuración
     */
    public function store(Request $request, Proyecto $proyecto)
    {
        $this->verificarCreador($proyecto);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'tipo' => 'required|in:DOCUMENTO,CODIGO,SCRIPT_BD,CONFIGURACION,OTRO',
            'commit_url' => 'nullable|string|max:500', // URL del commit de GitHub
        ]);

        // Generar código EC automáticamente
        $countElementos = $proyecto->elementosConfiguracion()->count();
        $codigoEc = $proyecto->codigo . '-EC-' . str_pad($countElementos + 1, 3, '0', STR_PAD_LEFT);

        // Crear el elemento de configuración
        $elemento = new ElementoConfiguracion();
        $elemento->id = (string) Str::uuid();
        $elemento->proyecto_id = $proyecto->id;
        $elemento->codigo_ec = $codigoEc;
        $elemento->titulo = $validated['titulo'];
        $elemento->descripcion = $validated['descripcion'];
        $elemento->tipo = $validated['tipo'];
    $elemento->estado = 'PENDIENTE';
        $elemento->creado_por = Auth::user()->id;
        $elemento->save();

        // Si hay URL de commit, registrar el commit en la base de datos
        $commitId = null;
        if ($request->filled('commit_url')) {
            // Extraer metadatos del commit desde GitHub API
            $datosCommit = $this->obtenerDatosCommitGitHub($validated['commit_url'], $elemento->id);

            if ($datosCommit) {
                $commit = new CommitRepositorio();
                $commit->id = (string) Str::uuid();
                $commit->url_repositorio = $datosCommit['url_repositorio'];
                $commit->hash_commit = $datosCommit['hash_commit'];
                $commit->autor = $datosCommit['autor'];
                $commit->mensaje = $datosCommit['mensaje'];
                $commit->fecha_commit = $datosCommit['fecha_commit'];
                $commit->ec_id = $elemento->id;
                $commit->save();

                $commitId = $commit->id;
            }
        }

        // Crear primera versión (inicial en borrador) sólo si no existen versiones previas
        if ($elemento->versiones()->count() === 0) {
            $version = new VersionEC();
            $version->id = (string) Str::uuid();
            $version->ec_id = $elemento->id;
            // Usamos 0.0.0 como versión inicial (borrador) — se marcará como aprobada/lanzada mediante flujo de revisiones
            $version->version = '0.0.0';
            $version->registro_cambios = 'Versión inicial';
            $version->commit_id = $commitId; // Asociar commit si existe
                // Asignar estado sólo si la columna existe en la tabla (para compatibilidad con migraciones actuales)
                if (Schema::hasColumn('versiones_ec', 'estado')) {
                    $version->estado = 'PENDIENTE';
                }
            $version->creado_por = Auth::user()->id;
            $version->save();

            // Asignar como versión actual
            $elemento->version_actual_id = $version->id;
            $elemento->save();
        }

        return redirect()
            ->route('proyectos.elementos.index', $proyecto)
            ->with('success', 'Elemento de configuración creado exitosamente.');
    }

    /**
     * Muestra formulario de edición
     */
    public function edit(Proyecto $proyecto, ElementoConfiguracion $elemento)
    {
        $this->verificarCreador($proyecto);

        if ($elemento->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        // Obtener todos los miembros de todos los equipos del proyecto
        $miembrosEquipo = collect();
        foreach ($proyecto->equipos as $equipo) {
            $miembrosEquipo = $miembrosEquipo->merge($equipo->miembros);
        }
        // Eliminar duplicados si un usuario está en varios equipos
        $miembrosEquipo = $miembrosEquipo->unique('id');

        return view('gestionProyectos.elementos.edit', compact('proyecto', 'elemento', 'miembrosEquipo'));
    }

    /**
     * Actualiza un elemento de configuración (solo metadatos, NO crea versión)
     */
    public function update(Request $request, Proyecto $proyecto, ElementoConfiguracion $elemento)
    {
        $this->verificarCreador($proyecto);

        if ($elemento->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'tipo' => 'required|in:DOCUMENTO,CODIGO,SCRIPT_BD,CONFIGURACION,OTRO',
            'estado' => 'required|in:PENDIENTE,BORRADOR,EN_REVISION,APROBADO,LIBERADO,OBSOLETO',
        ]);

        // Actualizar solo metadatos del elemento (NO se crea versión)
        $elemento->titulo = $validated['titulo'];
        $elemento->descripcion = $validated['descripcion'];
        $elemento->tipo = $validated['tipo'];
        $elemento->estado = $validated['estado'];
        $elemento->save();

        return redirect()
            ->route('proyectos.elementos.index', $proyecto)
            ->with('success', 'Elemento de configuración actualizado exitosamente.');
    }

    /**
     * Muestra la vista de revisión del EC
     */
    public function review(Proyecto $proyecto, ElementoConfiguracion $elemento)
    {
        $this->verificarCreador($proyecto);

        if ($elemento->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        return view('gestionProyectos.elementos.review', compact('proyecto', 'elemento'));
    }

    /**
     * Aprueba el EC y crea una nueva versión
     */
    public function approve(Request $request, Proyecto $proyecto, ElementoConfiguracion $elemento)
    {
        $this->verificarCreador($proyecto);

        if ($elemento->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        $validated = $request->validate([
            'commit_url' => 'required|string|max:500',
            'registro_cambios' => 'nullable|string',
        ]);

        // Obtener datos del commit desde GitHub
        $datosCommit = $this->obtenerDatosCommitGitHub($validated['commit_url'], $elemento->id);

        if (!$datosCommit) {
            return back()->withErrors(['commit_url' => 'No se pudo obtener información del commit. Verifica la URL.']);
        }

        // Crear registro de commit
        $commit = new CommitRepositorio();
        $commit->id = (string) Str::uuid();
        $commit->url_repositorio = $datosCommit['url_repositorio'];
        $commit->hash_commit = $datosCommit['hash_commit'];
        $commit->autor = $datosCommit['autor'];
        $commit->mensaje = $datosCommit['mensaje'];
        $commit->fecha_commit = $datosCommit['fecha_commit'];
        $commit->ec_id = $elemento->id;
        $commit->save();

        // Calcular nueva versión
        $versionActual = $elemento->versionActual;
        $versionParts = explode('.', $versionActual ? $versionActual->version : '0.0.0');

        // Incrementar versión: si es 0.0.0 → 1.0.0, sino incrementar minor
        if ($versionParts[0] === '0') {
            $nuevaVersion = '1.0.0';
        } else {
            $versionParts[1] = (int)$versionParts[1] + 1;
            $versionParts[2] = 0; // Reset patch
            $nuevaVersion = implode('.', $versionParts);
        }

        // Crear nueva versión APROBADA
        $version = new VersionEC();
        $version->id = (string) Str::uuid();
        $version->ec_id = $elemento->id;
        $version->version = $nuevaVersion;
        $version->registro_cambios = $validated['registro_cambios'] ?? 'Versión aprobada';
        $version->commit_id = $commit->id;

        if (Schema::hasColumn('versiones_ec', 'estado')) {
            $version->estado = 'APROBADO';
        }

        $version->creado_por = Auth::user()->id;
        $version->aprobado_por = Auth::user()->id;
        $version->aprobado_en = now();
        $version->save();

        // Actualizar versión actual del elemento
        $elemento->version_actual_id = $version->id;
        $elemento->estado = 'APROBADO';
        $elemento->save();

        return redirect()
            ->route('proyectos.elementos.index', $proyecto)
            ->with('success', "Elemento aprobado exitosamente. Nueva versión: v{$nuevaVersion}");
    }

    /**
     * Elimina un elemento de configuración
     */
    public function destroy(Proyecto $proyecto, ElementoConfiguracion $elemento)
    {
        $this->verificarCreador($proyecto);

        if ($elemento->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        // Eliminar versiones
        $elemento->versiones()->delete();

        // Eliminar elemento
        $elemento->delete();

        return redirect()
            ->route('proyectos.elementos.index', $proyecto)
            ->with('success', 'Elemento de configuración eliminado exitosamente.');
    }

    /**
     * Obtiene datos de un commit desde GitHub usando su API pública
     *
     * @param string $commitUrl URL del commit de GitHub (ej: github.com/user/repo/commit/abc123)
     * @param string $ecId ID del elemento de configuración
     * @return array|null Array con datos del commit o null si falla
     */
    private function obtenerDatosCommitGitHub($commitUrl, $ecId)
    {
        try {
            // Extraer componentes de la URL del commit
            // Formato: github.com/OWNER/REPO/commit/HASH
            $pattern = '#github\.com/([^/]+)/([^/]+)/commit/([a-f0-9]+)#i';

            if (!preg_match($pattern, $commitUrl, $matches)) {
                return null; // URL no es formato válido
            }

            $owner = $matches[1];
            $repo = $matches[2];
            $hash = $matches[3];

            // Construir URL de API de GitHub para obtener datos del commit
            $apiUrl = "https://api.github.com/repos/{$owner}/{$repo}/commits/{$hash}";

            // Hacer petición a la API
            $response = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/vnd.github.v3+json',
                    'User-Agent' => 'SGCS-Laravel-App'
                ])
                ->get($apiUrl);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            // Extraer datos del commit
            return [
                'url_repositorio' => "https://github.com/{$owner}/{$repo}",
                'hash_commit' => $data['sha'] ?? $hash,
                'autor' => $data['commit']['author']['name'] ?? 'Desconocido',
                'mensaje' => $data['commit']['message'] ?? '',
                'fecha_commit' => $data['commit']['author']['date'] ?? now(),
            ];

        } catch (\Exception $e) {
            Log::warning('Error al obtener datos del commit de GitHub: ' . $e->getMessage());
            return null;
        }
    }
}
