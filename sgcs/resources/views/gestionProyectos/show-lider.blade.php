<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-4 flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('proyectos.index') }}" class="hover:text-gray-900">Proyectos</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-medium">{{ $proyecto->nombre }}</span>
            </div>

            <!-- Header del Proyecto -->
            <div class="card bg-white shadow-sm mb-6">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            <div class="avatar placeholder">
                                <div class="bg-gray-200 text-black rounded-xl w-16 h-16 flex items-center justify-center">
                                    <span class="text-xl font-bold">{{ strtoupper(substr($proyecto->codigo ?? $proyecto->nombre, 0, 2)) }}</span>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $proyecto->nombre }}</h1>
                                <p class="text-sm text-gray-600 mt-1">{{ $proyecto->codigo }} • {{ ucfirst($proyecto->metodologia) }}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-xs text-gray-600">
                                        Creado por: <span class="font-medium">{{ $proyecto->creador->nombre_completo }}</span>
                                    </span>
                                    <span class="text-xs text-gray-600">
                                        {{ $proyecto->creado_en->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($esCreador)
                                <span class="badge bg-yellow-100 text-yellow-800 border-0">Creador</span>
                            @else
                                <span class="badge bg-orange-100 text-orange-800 border-0">Líder</span>
                            @endif
                            <span class="badge bg-green-100 text-green-800 border-0">Activo</span>
                        </div>
                    </div>

                    @if($proyecto->descripcion)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-700">{{ $proyecto->descripcion }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tabs de navegación (LÍDER tiene acceso a TODO) -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-6">
                        <a href="#" class="py-3 px-1 border-b-2 border-black text-sm font-medium text-gray-900">
                            Dashboard
                        </a>
                        <a href="#" class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
                            Equipos
                        </a>
                        <a href="{{ route('proyectos.elementos.index', $proyecto) }}" class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
                            Elementos Config.
                        </a>
                        <a href="{{ route('proyectos.tareas.index', $proyecto) }}" class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
                            Cronograma
                        </a>
                        <a href="#" class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
                            Solicitudes Cambio
                        </a>
                        <a href="#" class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
                            Liberaciones
                        </a>
                        <a href="#" class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
                            Reportes
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Contenido: Dashboard COMPLETO del LÍDER -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- Indicadores -->
                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Equipos</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $proyecto->equipos->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Miembros</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">
                                    {{ $proyecto->equipos->sum(function($equipo) { return $equipo->miembros->count(); }) }}
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Elem. Config.</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">0</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Cambios</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">0</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Acciones Rápidas (solo para LÍDER) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                <div class="card bg-white shadow-sm border-l-4 border-blue-500">
                    <div class="card-body">
                        <h3 class="font-semibold text-gray-900 mb-2">Crear Elemento de Configuración</h3>
                        <p class="text-sm text-gray-600 mb-3">Agrega un nuevo elemento al proyecto</p>
                        <a href="{{ route('proyectos.elementos.create', $proyecto) }}" class="btn btn-sm bg-blue-500 text-white hover:bg-blue-600 border-0">
                            Crear Elemento
                        </a>
                    </div>
                </div>

                <div class="card bg-white shadow-sm border-l-4 border-orange-500">
                    <div class="card-body">
                        <h3 class="font-semibold text-gray-900 mb-2">Nueva Solicitud de Cambio</h3>
                        <p class="text-sm text-gray-600 mb-3">Registra una solicitud de cambio</p>
                        <button class="btn btn-sm bg-orange-500 text-white hover:bg-orange-600 border-0">Crear Solicitud</button>
                    </div>
                </div>

                <div class="card bg-white shadow-sm border-l-4 border-green-500">
                    <div class="card-body">
                        <h3 class="font-semibold text-gray-900 mb-2">Programar Liberación</h3>
                        <p class="text-sm text-gray-600 mb-3">Planifica una nueva liberación</p>
                        <button class="btn btn-sm bg-green-500 text-white hover:bg-green-600 border-0">Crear Liberación</button>
                    </div>
                </div>
            </div>

            <!-- Lista de Equipos -->
            <div class="card bg-white shadow-sm mt-6">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Equipos del Proyecto</h2>
                        <button class="btn btn-sm bg-black text-white hover:bg-gray-800">Gestionar Equipos</button>
                    </div>

                    <div class="space-y-4">
                        @foreach($proyecto->equipos as $equipo)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $equipo->nombre }}</h3>
                                        <p class="text-sm text-gray-600">{{ $equipo->miembros->count() }} miembros</p>
                                    </div>
                                    @if($equipo->lider)
                                        <span class="badge bg-orange-100 text-orange-800 border-0">
                                            Líder: {{ $equipo->lider->nombre_completo }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Miembros del equipo -->
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach($equipo->miembros as $miembro)
                                        <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-full">
                                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-bold text-gray-700">{{ substr($miembro->nombre_completo, 0, 1) }}</span>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $miembro->nombre_completo }}</span>
                                            <span class="text-xs text-gray-500">• {{ $miembro->rol_proyecto->nombre ?? 'Sin rol' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
