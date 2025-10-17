<?php

namespace App\Http\Controllers\GestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Equipo;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Metodologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProyectoController extends Controller
{
    /**
     * Mostrar lista de TODOS los proyectos (creados + asignados).
     */
    public function index()
    {
        $usuarioId = Auth::user()->id;

        // Proyectos donde soy CREADOR
        $proyectosCreadosCollection = Proyecto::with(['equipos', 'creador'])
            ->where('creado_por', $usuarioId)
            ->get();

        // Resolver metodologías para proyectos creados
        $metIdsCreados = $proyectosCreadosCollection->pluck('id_metodologia')->filter()->unique()->values()->all();
        $metMapCreados = Metodologia::whereIn('id_metodologia', $metIdsCreados)->get()->keyBy('id_metodologia')->map(fn($m) => $m->nombre);

        $proyectosCreados = $proyectosCreadosCollection->map(function($proyecto) use ($metMapCreados) {
            $metNombre = $metMapCreados[$proyecto->id_metodologia] ?? 'No especificada';

            return [
                'id' => $proyecto->id,
                'codigo' => $proyecto->codigo,
                'nombre' => $proyecto->nombre,
                'id_metodologia' => $proyecto->id_metodologia,
                'metodologia' => $metNombre,
                'total_equipos' => $proyecto->equipos->count(),
                'total_miembros' => $proyecto->equipos->sum(fn($e) => $e->miembros()->count()),
                'mi_rol' => 'Creador',
                'creado_en' => $proyecto->creado_en,
                'estado' => 'Activo', // temporal
            ];
        });

        // Proyectos donde soy MIEMBRO
        $proyectosAsignados = Proyecto::with(['equipos' => function($query) use ($usuarioId) {
                $query->whereHas('miembros', function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                })->with(['miembros' => function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                }]);
            }])
            ->whereHas('equipos.miembros', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->where('creado_por', '!=', $usuarioId)
            ->get();

        // Resolver metodologías para proyectos en los que participa
        $metIdsAsignados = $proyectosAsignados->pluck('id_metodologia')->filter()->unique()->values()->all();
        $metMapAsignados = Metodologia::whereIn('id_metodologia', $metIdsAsignados)->get()->keyBy('id_metodologia')->map(fn($m) => $m->nombre);

        $proyectosAsignados = $proyectosAsignados->map(function($proyecto) use ($metMapAsignados) {
                $miEquipo = $proyecto->equipos->first();
                $miembro = $miEquipo->miembros->first();

                // Obtener el rol desde el pivot
                $rolId = $miembro->pivot->rol_id;
                $miRol = \App\Models\Rol::find($rolId)->nombre ?? 'Miembro';

                $metNombre = $metMapAsignados[$proyecto->id_metodologia] ?? 'No especificada';

                return [
                    'id' => $proyecto->id,
                    'codigo' => $proyecto->codigo,
                    'nombre' => $proyecto->nombre,
                    'id_metodologia' => $proyecto->id_metodologia,
                    'metodologia' => $metNombre,
                    'total_equipos' => $proyecto->equipos->count(),
                    'total_miembros' => $proyecto->equipos->sum(fn($e) => $e->miembros()->count()),
                    'mi_rol' => $miRol,
                    'nombre_equipo' => $miEquipo->nombre ?? 'Sin equipo',
                    'creado_en' => $proyecto->creado_en,
                    'estado' => 'Activo', // temporal
                    ];
                });

        // Combinar ambas colecciones y ordenar por fecha
        $todosLosProyectos = $proyectosCreados->concat($proyectosAsignados)
            ->sortByDesc('creado_en')
            ->values();

        return view('gestionProyectos.index', compact('todosLosProyectos'));
    }

    /**
     * Mostrar dashboard interno de un proyecto específico.
     */
    public function show(Proyecto $proyecto)
    {
        // Verificar que el usuario tenga acceso (es creador o miembro)
        $usuarioId = Auth::user()->id;
        $esCreador = $proyecto->creado_por === $usuarioId;
        $esMiembro = $proyecto->equipos()->whereHas('miembros', function($q) use ($usuarioId) {
            $q->where('usuario_id', $usuarioId);
        })->exists();

        if (!$esCreador && !$esMiembro) {
            abort(403, 'No tienes acceso a este proyecto.');
        }

        // Cargar relaciones con eager loading del rol desde el pivot
        $proyecto->load([
            'equipos' => function($query) {
                $query->with(['miembros' => function($q) {
                    // Aquí los miembros vienen con el pivot automáticamente
                }, 'lider']);
            },
            'creador'
        ]);

        // Detectar el ROL del usuario en este proyecto
        $miRol = null;
        $miEquipo = null;

        if ($esCreador) {
            $miRol = 'creador';
        } else {
            // Buscar el rol del usuario en algún equipo
            foreach ($proyecto->equipos as $equipo) {
                $miembro = $equipo->miembros->firstWhere('id', $usuarioId);
                if ($miembro) {
                    $rolId = $miembro->pivot->rol_id;
                    $rolObj = \App\Models\Rol::find($rolId);
                    $miRol = strtolower($rolObj->nombre); // 'líder', 'desarrollador', etc.
                    $miEquipo = $equipo;
                    break;
                }
            }
        }

        // Cargar los roles manualmente para cada miembro
        foreach ($proyecto->equipos as $equipo) {
            foreach ($equipo->miembros as $miembro) {
                $miembro->rol_proyecto = \App\Models\Rol::find($miembro->pivot->rol_id);
            }
        }

        // Decidir qué vista mostrar según el rol
        $vista = 'gestionProyectos.show'; // vista por defecto

        if ($esCreador || $miRol === 'líder') {
            $vista = 'gestionProyectos.show-lider';
        } elseif ($miRol === 'desarrollador') {
            $vista = 'gestionProyectos.show-desarrollador';
        }

        return view($vista, compact('proyecto', 'esCreador', 'miRol', 'miEquipo'));
    }

    /**
     * Mostrar el formulario para crear un nuevo proyecto (Paso 1).
     */
    public function create()
    {
        return view('gestionProyectos.create');
    }

    /**
     * Guardar el proyecto en sesión y mostrar el formulario de equipos (Paso 2).
     */
    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['nullable', 'string', 'max:50', 'unique:proyectos,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'id_metodologia' => ['required', 'exists:metodologias,id_metodologia'],
        ]);

        // Guardar datos del proyecto en sesión
        session(['proyecto_temp' => $validated]);

        // Redirigir al paso 2: Crear equipos
        return redirect()->route('proyectos.create-teams');
    }

    /**
     * Mostrar el formulario del paso 2 (crear equipos).
     */
    public function createTeams()
    {
        $proyectoData = session('proyecto_temp');

        if (!$proyectoData) {
            return redirect()->route('proyectos.create')
                ->with('error', 'Debes completar el Paso 1 primero.');
        }

        // Si la sesión tiene id_metodologia, resolver el nombre legible y añadirlo
        if (isset($proyectoData['id_metodologia'])) {
            $met = \App\Models\Metodologia::find($proyectoData['id_metodologia']);
            $proyectoData['metodologia'] = $met ? $met->nombre : null;
        } else {
            // por compatibilidad con versiones antiguas
            $proyectoData['metodologia'] = $proyectoData['metodologia'] ?? null;
        }

        return view('gestionProyectos.create-teams', compact('proyectoData'));
    }

    /**
     * Guardar equipos en sesión y mostrar el formulario de asignación de miembros (Paso 3).
     */
    public function storeStep2(Request $request)
    {
        $request->validate([
            'equipos' => ['required', 'array', 'min:1'],
            'equipos.*.nombre' => ['required', 'string', 'max:255'],
            'equipos.*.descripcion' => ['nullable', 'string'],
        ]);

        $proyectoData = session('proyecto_temp');

        if (!$proyectoData) {
            return redirect()->route('proyectos.create')
                ->with('error', 'Sesión expirada. Por favor, inicia el proceso nuevamente.');
        }

        // Guardar equipos en sesión
        session(['equipos_temp' => $request->equipos]);

        // Redirigir al paso 3: Asignar miembros
        return redirect()->route('proyectos.assign-members');
    }

    /**
     * Mostrar el formulario del paso 3 (asignar miembros a equipos).
     */
    public function assignMembers()
    {
        $proyectoData = session('proyecto_temp');
        $equiposData = session('equipos_temp');

        if (!$proyectoData || !$equiposData) {
            return redirect()->route('proyectos.create')
                ->with('error', 'Debes completar los pasos anteriores primero.');
        }

        $usuarios = Usuario::select('id', 'nombre_completo', 'correo')->orderBy('nombre_completo')->get();
        $roles = Rol::orderBy('nombre')->get();

        return view('gestionProyectos.assign-members', compact('usuarios', 'roles', 'proyectoData', 'equiposData'));
    }

    /**
     * Guardar el proyecto completo con equipos y miembros (transacción).
     */
    public function store(Request $request)
    {
        $request->validate([
            'miembros' => ['required', 'array', 'min:1'],
            'miembros.*.usuario_id' => ['required', 'exists:usuarios,id'],
            'miembros.*.rol_id' => ['required', 'exists:roles,id'],
            'miembros.*.equipo_index' => ['required', 'integer', 'min:0'],
        ]);

        // Recuperar datos desde la sesión
        $proyectoData = session('proyecto_temp');
        $equiposData = session('equipos_temp');

        if (!$proyectoData || !$equiposData) {
            return redirect()->route('proyectos.create')
                ->with('error', 'Sesión expirada. Por favor, inicia el proceso nuevamente.');
        }

        // ...validación eliminada: ya no es obligatorio que cada equipo tenga un líder...

        DB::beginTransaction();

        try {
            // 1️⃣ Crear el proyecto (con el usuario actual como creador)
            $proyecto = Proyecto::create([
                'id' => Str::uuid()->toString(),
                'codigo' => $proyectoData['codigo'],
                'nombre' => $proyectoData['nombre'],
                'descripcion' => $proyectoData['descripcion'],
                'id_metodologia' => $proyectoData['id_metodologia'],
                'creado_por' => Auth::user()->id, // ← UUID del usuario logueado
            ]);

            // 2️⃣ Crear los equipos y guardar sus IDs
            $equiposCreados = [];
            foreach ($equiposData as $index => $equipoData) {
                $equipoId = Str::uuid()->toString();

                Equipo::create([
                    'id' => $equipoId,
                    'proyecto_id' => $proyecto->id,
                    'nombre' => $equipoData['nombre'],
                    'lider_id' => Auth::user()->id, // Líder temporal (el creador del proyecto)
                ]);

                $equiposCreados[$index] = $equipoId;
            }

            // 3️⃣ Asignar miembros a sus equipos
            $lideresPorEquipo = [];

            foreach ($request->miembros as $miembro) {
                $equipoIndex = $miembro['equipo_index'];
                $equipoId = $equiposCreados[$equipoIndex];

                DB::table('miembros_equipo')->insert([
                    'equipo_id' => $equipoId,
                    'usuario_id' => $miembro['usuario_id'],
                    'rol_id' => $miembro['rol_id'],
                ]);

                // Si es líder (rol_id == 2), guardarlo para actualizar después
                if ($miembro['rol_id'] == 2 && !isset($lideresPorEquipo[$equipoId])) {
                    $lideresPorEquipo[$equipoId] = $miembro['usuario_id'];
                }
            }

            // 4️⃣ Actualizar líderes de equipos
            foreach ($lideresPorEquipo as $equipoId => $liderId) {
                Equipo::where('id', $equipoId)->update(['lider_id' => $liderId]);
            }

            // Limpiar sesión
            session()->forget(['proyecto_temp', 'equipos_temp']);

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', '¡Proyecto "' . $proyecto->nombre . '" creado exitosamente con ' . count($equiposCreados) . ' equipo(s) y ' . count($request->miembros) . ' miembro(s)!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('proyectos.create')
                ->with('error', 'Error al crear el proyecto: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar el proceso y limpiar la sesión.
     */
    public function cancel()
    {
        session()->forget(['proyecto_temp', 'equipos_temp']);

        return redirect()->route('dashboard')
            ->with('info', 'Proceso de creación de proyecto cancelado.');
    }
}
