<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Bienvenida simplificada -->
            <div class="card bg-white text-black shadow-sm">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-2xl text-black">👋 Hola, {{ Auth::user()->nombre_completo }}</h3>
                            <p class="text-sm text-gray-700 mt-1">Bienvenido al panel. Aquí verás tus proyectos y acciones rápidas.</p>
                        </div>
                        <div>
                            @if (Route::has('proyectos.create'))
                                <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-2 border border-black text-black bg-white px-4 py-2 rounded-md hover:bg-black hover:text-white transition">+
                                    Nuevo Proyecto
                                </a>
                            @else
                                <a href="#" class="inline-flex items-center gap-2 border border-black text-black bg-white px-4 py-2 rounded-md">+
                                    Nuevo Proyecto
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicadores Generales -->
            <div class="stats stats-vertical lg:stats-horizontal shadow w-full bg-white text-black">
                <div class="stat bg-white text-black">
                    <div class="stat-figure text-black opacity-80">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title text-gray-800">Proyectos Creados</div>
                    <div class="stat-value">{{ $proyectosCreados }}</div>
                    <div class="stat-desc text-gray-600">Proyectos que yo creé</div>
                </div>

                <div class="stat bg-white text-black">
                    <div class="stat-figure text-black opacity-80">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                    </div>
                    <div class="stat-title text-gray-800">Proyectos Asignados</div>
                    <div class="stat-value">{{ $proyectosAsignados }}</div>
                    <div class="stat-desc text-gray-600">Soy miembro del equipo</div>
                </div>

                <div class="stat bg-white text-black">
                    <div class="stat-figure text-black opacity-80">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div class="stat-title text-gray-800">Cambios Pendientes</div>
                    <div class="stat-value">{{ $cambiosPendientes }}</div>
                    <div class="stat-desc text-gray-600">Requieren revisión</div>
                </div>

                <div class="stat bg-white text-black">
                    <div class="stat-figure text-black opacity-80">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="stat-title text-gray-800">Elem. Configuración</div>
                    <div class="stat-value">{{ $elementosConfiguracion }}</div>
                    <div class="stat-desc text-gray-600">Elementos registrados</div>
                </div>
            </div>

            <!-- Grid Principal: 2 secciones -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- 📁 Proyectos que YO CREÉ -->
                <div class="card bg-white text-black shadow">
                    <div class="card-body">
                        <h2 class="card-title flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Proyectos que Creé
                        </h2>

                        @if($misProyectos->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="font-medium">No has creado proyectos aún</p>
                                <p class="text-sm mt-1">Crea tu primer proyecto para comenzar</p>
                            </div>
                        @else
                            <div class="space-y-3 mt-4">
                                @foreach($misProyectos as $proyecto)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex items-start gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-gray-200 text-black rounded-lg w-12 h-12 flex items-center justify-center">
                                                    <span class="text-sm font-bold">{{ $proyecto['iniciales'] }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <h3 class="font-bold text-black">{{ $proyecto['nombre'] }}</h3>
                                                        <p class="text-xs text-gray-600">{{ $proyecto['codigo'] }} • {{ ucfirst($proyecto['metodologia'] ?? 'No especificada') }}</p>
                                                    </div>
                                                    <span class="badge badge-outline badge-sm text-gray-800">{{ $proyecto['estado'] }}</span>
                                                </div>
                                                <div class="mt-3 flex items-center gap-4 text-xs text-gray-600">
                                                    <span> {{ $proyecto['total_miembros'] }} miembros</span>
                                                    <span> {{ $proyecto['total_equipos'] }} equipos</span>
                                                </div>
                                                <div class="mt-2 flex items-center gap-2">
                                                    <progress class="progress progress-primary w-full h-2" value="{{ $proyecto['progreso'] }}" max="100"></progress>
                                                    <span class="text-xs text-gray-800 font-medium">{{ $proyecto['progreso'] }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 👥 Proyectos donde SOY MIEMBRO -->
                <div class="card bg-white text-black shadow">
                    <div class="card-body">
                        <h2 class="card-title flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Proyectos Asignados
                        </h2>

                        @if($proyectosParticipando->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p class="font-medium">No estás asignado a proyectos</p>
                                <p class="text-sm mt-1">Te notificaremos cuando seas agregado</p>
                            </div>
                        @else
                            <div class="space-y-3 mt-4">
                                @foreach($proyectosParticipando as $proyecto)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex items-start gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-gray-200 text-black rounded-lg w-12 h-12 flex items-center justify-center">
                                                    <span class="text-sm font-bold">{{ $proyecto['iniciales'] }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <h3 class="font-bold text-black">{{ $proyecto['nombre'] }}</h3>
                                                        <p class="text-xs text-gray-600">{{ $proyecto['codigo'] }} • {{ ucfirst($proyecto['metodologia'] ?? 'No especificada') }}</p>
                                                    </div>
                                                    <span class="badge badge-outline badge-sm text-gray-800">{{ $proyecto['estado'] }}</span>
                                                </div>
                                                <div class="mt-2 flex items-center gap-2">
                                                    <span class="badge badge-sm bg-gray-100 text-gray-800 border-0">{{ $proyecto['mi_rol'] }}</span>
                                                    <span class="text-xs text-gray-600">en {{ $proyecto['nombre_equipo'] }}</span>
                                                </div>
                                                <div class="mt-2 flex items-center gap-2">
                                                    <progress class="progress progress-primary w-full h-2" value="{{ $proyecto['progreso'] }}" max="100"></progress>
                                                    <span class="text-xs text-gray-800 font-medium">{{ $proyecto['progreso'] }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>            <!-- Acciones Rápidas eliminadas por petición del usuario -->

        </div>
    </div>
</x-app-layout>
