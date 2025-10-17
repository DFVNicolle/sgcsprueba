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
                                    @if($miEquipo)
                                        <span class="text-xs text-gray-600">
                                            Mi equipo: <span class="font-medium">{{ $miEquipo->nombre }}</span>
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-600">
                                        {{ $proyecto->creado_en->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="badge bg-blue-100 text-blue-800 border-0">💻 Desarrollador</span>
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

            <!-- Tabs de navegación (DESARROLLADOR solo ve lo necesario) -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-6">
                        <a href="#" class="py-3 px-1 border-b-2 border-black text-sm font-medium text-gray-900">
                            Mis Tareas
                        </a>
                        <a href="#" class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
                            Elementos Asignados
                        </a>
                        <a href="#" class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
                            Cambios
                        </a>
                        <a href="#" class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
                            Mi Equipo
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Contenido: Dashboard del DESARROLLADOR (enfocado en TAREAS) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Indicadores personalizados -->
                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Tareas Asignadas</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">0</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">En Progreso</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">0</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Completadas</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">0</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Mis Tareas Pendientes -->
            <div class="card bg-white shadow-sm mt-6">
                <div class="card-body">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Mis Tareas Pendientes</h2>

                    <!-- Empty state (temporal) -->
                    <div class="text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="font-medium">No tienes tareas asignadas</p>
                        <p class="text-sm mt-1">Las tareas aparecerán aquí cuando sean asignadas</p>
                    </div>
                </div>
            </div>

            <!-- Reportar Avance Rápido -->
            <div class="card bg-white shadow-sm mt-6 border-l-4 border-blue-500">
                <div class="card-body">
                    <h3 class="font-semibold text-gray-900 mb-2">Reportar Avance</h3>
                    <p class="text-sm text-gray-600 mb-4">Actualiza el progreso de tus tareas</p>

                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selecciona la tarea</label>
                            <select class="select select-bordered w-full bg-white text-gray-800 border-gray-300">
                                <option disabled selected>Selecciona una tarea</option>
                                <option>Tarea ejemplo 1</option>
                                <option>Tarea ejemplo 2</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Progreso (%)</label>
                            <input type="range" min="0" max="100" value="50" class="range range-primary" />
                            <div class="flex justify-between text-xs text-gray-600 mt-1">
                                <span>0%</span>
                                <span>50%</span>
                                <span>100%</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Comentarios</label>
                            <textarea class="textarea textarea-bordered w-full bg-white text-gray-800 border-gray-300" rows="3" placeholder="Describe tu avance..."></textarea>
                        </div>

                        <button type="submit" class="btn bg-blue-500 text-white hover:bg-blue-600 border-0">Guardar Avance</button>
                    </form>
                </div>
            </div>

            <!-- Mi Equipo -->
            @if($miEquipo)
            <div class="card bg-white shadow-sm mt-6">
                <div class="card-body">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Mi Equipo: {{ $miEquipo->nombre }}</h2>

                    <div class="space-y-3">
                        @if($miEquipo->lider)
                            <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-lg">
                                <div class="w-10 h-10 bg-orange-200 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-orange-800">{{ substr($miEquipo->lider->nombre_completo, 0, 1) }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $miEquipo->lider->nombre_completo }}</p>
                                    <p class="text-xs text-gray-600">Líder del equipo</p>
                                </div>
                                <span class="badge bg-orange-100 text-orange-800 border-0">Líder</span>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($miEquipo->miembros as $miembro)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-gray-700">{{ substr($miembro->nombre_completo, 0, 1) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $miembro->nombre_completo }}</p>
                                        <p class="text-xs text-gray-600">{{ $miembro->rol_proyecto->nombre ?? 'Sin rol' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
