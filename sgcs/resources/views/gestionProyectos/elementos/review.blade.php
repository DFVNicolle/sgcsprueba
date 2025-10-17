<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Revisar Elemento de Configuración
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Código: <span class="font-mono font-bold">{{ $elemento->codigo_ec }}</span>
                </p>
            </div>
            <a href="{{ route('proyectos.elementos.index', $proyecto) }}" class="btn btn-ghost btn-sm">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Card principal -->
            <div class="card bg-white shadow-xl mb-6">
                <div class="card-body">
                    <!-- Estado actual -->
                    <div class="alert {{ $elemento->estado === 'EN_REVISION' ? 'alert-warning' : 'alert-info' }} mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Estado: {{ $elemento->estado }}</h3>
                            <div class="text-xs">
                                @if($elemento->estado === 'EN_REVISION')
                                    Este elemento está pendiente de revisión. Revisa los detalles y decide si aprobarlo.
                                @else
                                    Puedes revisar este elemento en cualquier momento.
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Información del EC -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Título</label>
                            <p class="text-lg font-bold text-gray-800">{{ $elemento->titulo }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-600">Descripción</label>
                            <p class="text-gray-700">{{ $elemento->descripcion }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Tipo</label>
                                <p class="text-gray-800">
                                    @switch($elemento->tipo)
                                        @case('DOCUMENTO') 📄 Documento @break
                                        @case('CODIGO') 💻 Código Fuente @break
                                        @case('SCRIPT_BD') 🗄️ Script BD @break
                                        @case('CONFIGURACION') ⚙️ Configuración @break
                                        @default 📦 Otro
                                    @endswitch
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Creado por</label>
                                <p class="text-gray-800">{{ $elemento->creador->nombre_completo ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Versión actual -->
                    @if($elemento->versionActual)
                        <div class="divider"></div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-bold text-gray-800 mb-3">📌 Versión Actual</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Versión</label>
                                    <p class="text-lg font-mono text-blue-600">v{{ $elemento->versionActual->version }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Estado</label>
                                    <p>
                                        <span class="badge {{ $elemento->versionActual->estado === 'APROBADO' ? 'badge-success' : 'badge-warning' }}">
                                            {{ $elemento->versionActual->estado }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-span-2">
                                    <label class="text-xs font-semibold text-gray-600">Registro de cambios</label>
                                    <p class="text-sm text-gray-700">{{ $elemento->versionActual->registro_cambios ?? 'Sin descripción' }}</p>
                                </div>
                            </div>

                            @if($elemento->versionActual->commit)
                                <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <label class="text-xs font-semibold text-green-800">Commit asociado</label>
                                            <p class="text-sm font-mono text-green-900">{{ $elemento->versionActual->commit->hash_commit }}</p>
                                            <p class="text-xs text-green-700 mt-1">
                                                <strong>Autor:</strong> {{ $elemento->versionActual->commit->autor }} |
                                                <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($elemento->versionActual->commit->fecha_commit)->format('d/m/Y H:i') }}
                                            </p>
                                            <p class="text-xs text-green-700 italic mt-1">{{ $elemento->versionActual->commit->mensaje }}</p>
                                        </div>
                                        <a href="{{ $elemento->versionActual->commit->url_repositorio }}/commit/{{ $elemento->versionActual->commit->hash_commit }}"
                                           target="_blank"
                                           class="btn btn-xs btn-ghost text-green-700">
                                            Ver en GitHub →
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Historial de versiones -->
                    @if($elemento->versiones->count() > 1)
                        <div class="divider"></div>
                        <details class="collapse collapse-arrow bg-gray-50">
                            <summary class="collapse-title font-semibold text-gray-800">
                                📜 Historial de versiones ({{ $elemento->versiones->count() }})
                            </summary>
                            <div class="collapse-content">
                                <div class="space-y-2 mt-2">
                                    @foreach($elemento->versiones()->orderBy('creado_en', 'desc')->get() as $version)
                                        <div class="p-3 bg-white border rounded {{ $version->id === $elemento->version_actual_id ? 'border-blue-500' : 'border-gray-200' }}">
                                            <div class="flex items-center justify-between">
                                                <span class="font-mono font-bold text-blue-600">v{{ $version->version }}</span>
                                                <span class="text-xs text-gray-500">{{ $version->creado_en->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $version->registro_cambios }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </details>
                    @endif

                    <!-- Botones de acción -->
                    <div class="card-actions justify-end pt-6 border-t mt-6">
                        <a href="{{ route('proyectos.elementos.index', $proyecto) }}" class="btn btn-ghost text-black">
                            Cancelar
                        </a>
                        <a href="{{ route('proyectos.elementos.edit', [$proyecto, $elemento]) }}" class="btn btn-outline">
                            ✏️ Editar metadatos
                        </a>
                        <button onclick="modalAprobar.showModal()" class="btn bg-green-600 text-white hover:bg-green-700">
                            ✅ Aprobar y Crear Nueva Versión
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Modal: Aprobar EC -->
    <dialog id="modalAprobar" class="modal">
        <div class="modal-box max-w-2xl bg-white">
            <h3 class="font-bold text-lg text-black mb-4">🎉 Aprobar Elemento de Configuración</h3>
            <p class="text-sm text-gray-600 mb-4">
                Al aprobar este elemento, se creará una nueva versión y se marcará como <strong>APROBADO</strong>.
                Necesitas proporcionar la URL del commit de GitHub que representa esta versión aprobada.
            </p>

            <form method="POST" action="{{ route('proyectos.elementos.approve', [$proyecto, $elemento]) }}">
                @csrf

                <!-- URL del commit -->
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text text-black font-semibold">URL del Commit en GitHub *</span>
                    </label>
                    <input type="text"
                           name="commit_url"
                           required
                           class="input input-bordered w-full bg-white text-black"
                           placeholder="https://github.com/usuario/repo/commit/abc123def456...">
                    <label class="label">
                        <span class="label-text-alt text-gray-500">
                            Pega la URL completa del commit que representa esta versión aprobada
                        </span>
                    </label>
                </div>

                <!-- Registro de cambios -->
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text text-black font-semibold">Descripción de la nueva versión</span>
                    </label>
                    <textarea name="registro_cambios"
                              rows="3"
                              class="textarea textarea-bordered w-full bg-white text-black"
                              placeholder="Describe los cambios principales de esta versión aprobada..."></textarea>
                </div>

                <!-- Información de ayuda -->
                <div class="alert alert-info text-sm mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p><strong>¿Qué sucederá?</strong></p>
                        <ul class="list-disc list-inside text-xs mt-1">
                            <li>Se creará una nueva versión automáticamente (incremento de versión)</li>
                            <li>El estado del EC cambiará a <strong>APROBADO</strong></li>
                            <li>Se vinculará el commit de GitHub especificado</li>
                            <li>La nueva versión quedará como versión actual</li>
                        </ul>
                    </div>
                </div>

                <!-- Botones -->
                <div class="modal-action">
                    <button type="button" onclick="modalAprobar.close()" class="btn btn-ghost text-black">Cancelar</button>
                    <button type="submit" class="btn bg-green-600 text-white hover:bg-green-700">✅ Aprobar y Versionar</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    @if($errors->any())
        <script>
            setTimeout(() => {
                alert('Error: {{ $errors->first() }}');
            }, 100);
        </script>
    @endif
</x-app-layout>
