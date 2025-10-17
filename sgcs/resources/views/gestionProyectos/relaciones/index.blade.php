<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Relaciones del Elemento
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Proyecto: <span class="font-semibold">{{ $proyecto->nombre }}</span> →
                    Elemento: <span class="font-semibold">{{ $elemento->codigo_ec }} - {{ $elemento->titulo }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('proyectos.elementos.index', $proyecto) }}" class="btn btn-ghost btn-sm">
                    ← Volver a Elementos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Botón para crear nueva relación -->
            <div class="mb-6">
                     <a href="{{ route('proyectos.elementos.relaciones.create', [$proyecto, $elemento]) }}"
                         class="btn bg-black text-white gap-2 hover:bg-gray-800 border-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nueva Relación
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Relaciones Salientes -->
                <div class="card bg-white shadow-xl border border-gray-200">
                    <div class="card-body text-black">
                        <h3 class="card-title text-lg">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            Relaciones Salientes
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Este elemento se relaciona con los siguientes:
                        </p>

                        @if($relacionesDesde->isEmpty())
                            <div class="alert bg-gray-100 text-black border border-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>No hay relaciones salientes.</span>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($relacionesDesde as $relacion)
                                    <div class="border border-base-300 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="badge {{ $relacion->tipoBadge }} badge-sm">
                                                {{ $relacion->tipoRelacionNombre }}
                                            </span>
                                            <form action="{{ route('proyectos.elementos.relaciones.destroy', [$proyecto, $elemento, $relacion]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('¿Eliminar esta relación?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-ghost btn-xs text-error">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="font-semibold text-sm">
                                            {{ $relacion->elementoHacia->codigo_ec }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ $relacion->elementoHacia->titulo }}
                                        </div>

                                        @if($relacion->nota)
                                            <div class="text-xs text-gray-500 mt-2 italic">
                                                💬 {{ $relacion->nota }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Relaciones Entrantes -->
                <div class="card bg-white shadow-xl border border-gray-200">
                    <div class="card-body text-black">
                        <h3 class="card-title text-lg">
                            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Relaciones Entrantes
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Los siguientes elementos se relacionan con este:
                        </p>

                        @if($relacionesHacia->isEmpty())
                            <div class="alert bg-gray-100 text-black border border-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>No hay relaciones entrantes.</span>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($relacionesHacia as $relacion)
                                    <div class="border border-base-300 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="badge {{ $relacion->tipoBadge }} badge-sm">
                                                {{ $relacion->tipoRelacionNombre }}
                                            </span>
                                            <span class="text-xs text-gray-500">Solo lectura</span>
                                        </div>

                                        <div class="font-semibold text-sm">
                                            {{ $relacion->elementoDesde->codigo_ec }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ $relacion->elementoDesde->titulo }}
                                        </div>

                                        @if($relacion->nota)
                                            <div class="text-xs text-gray-500 mt-2 italic">
                                                💬 {{ $relacion->nota }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Información adicional -->
            <div class="alert alert-info mt-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-black">
                    <strong>Tipos de relaciones:</strong>
                    <ul class="list-disc list-inside mt-2">
                        <li><span class="badge badge-error badge-sm">Depende de</span> - Este elemento requiere otro elemento</li>
                        <li><span class="badge badge-warning badge-sm">Derivado de</span> - Este elemento es una extensión o derivación de otro</li>
                        <li><span class="badge badge-info badge-sm">Referencia a</span> - Este elemento hace referencia a otro</li>
                        <li><span class="badge badge-success badge-sm">Requerido por</span> - Este elemento es requerido por otro</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
