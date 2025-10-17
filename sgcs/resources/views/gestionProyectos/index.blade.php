<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header con título y botón -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📁 Proyectos</h1>
                    <p class="text-gray-600 mt-1">Gestiona todos tus proyectos desde aquí</p>
                </div>
                <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-2 bg-black text-white px-4 py-2 rounded-md hover:bg-gray-800 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Proyecto
                </a>
            </div>

            <!-- Tabla de proyectos -->
            <div class="card bg-white shadow-sm">
                <div class="card-body p-0">
                    @if($todosLosProyectos->isEmpty())
                        <!-- Empty State -->
                        <div class="text-center py-16 px-4">
                            <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay proyectos aún</h3>
                            <p class="text-gray-600 mb-6">Crea tu primer proyecto para comenzar</p>
                            <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-2 bg-black text-white px-6 py-3 rounded-md hover:bg-gray-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Crear Proyecto
                            </a>
                        </div>
                    @else
                        <!-- Tabla -->
                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Proyecto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Metodología</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Mi Rol</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Equipos</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Miembros</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Creado</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($todosLosProyectos as $proyecto)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="avatar placeholder">
                                                        <div class="bg-gray-200 text-black rounded-lg w-10 h-10 flex items-center justify-center">
                                                            <span class="text-xs font-bold">{{ strtoupper(substr($proyecto['codigo'] ?? $proyecto['nombre'], 0, 2)) }}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ $proyecto['nombre'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $proyecto['codigo'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $metodologia = $proyecto['metodologia'] ?? 'No especificada';
                                                    $metodologiaKey = strtolower($metodologia);
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($metodologiaKey === 'agil') bg-blue-100 text-blue-800
                                                    @elseif($metodologiaKey === 'cascada') bg-purple-100 text-purple-800
                                                    @else bg-green-100 text-green-800
                                                    @endif">
                                                    {{ ucfirst($metodologia) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($proyecto['mi_rol'] === 'Creador') bg-yellow-100 text-yellow-800
                                                    @elseif($proyecto['mi_rol'] === 'Líder') bg-orange-100 text-orange-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $proyecto['mi_rol'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="text-sm font-medium text-gray-900">{{ $proyecto['total_equipos'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="text-sm font-medium text-gray-900">{{ $proyecto['total_miembros'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $proyecto['estado'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $proyecto['creado_en']->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <a href="{{ route('proyectos.show', $proyecto['id']) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-black text-white text-xs font-medium rounded hover:bg-gray-800 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer con estadísticas -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <div>
                                    Mostrando <span class="font-medium text-gray-900">{{ $todosLosProyectos->count() }}</span> proyectos en total
                                </div>
                                <div class="flex items-center gap-4">
                                    <span> {{ $todosLosProyectos->where('mi_rol', 'Creador')->count() }} creados</span>
                                    <span> {{ $todosLosProyectos->where('mi_rol', '!=', 'Creador')->count() }} asignados</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
