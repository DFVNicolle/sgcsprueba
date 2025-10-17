<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editar Elemento de Configuración
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Código: <span class="font-mono font-bold">{{ $elemento->codigo_ec }}</span>
                </p>
            </div>
            <a href="{{ route('proyectos.elementos.index', $proyecto) }}" class="btn btn-ghost btn-sm">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card bg-white shadow-xl">
                <div class="card-body">
                    <form action="{{ route('proyectos.elementos.update', [$proyecto, $elemento]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Código EC (solo lectura) -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold text-black">Código EC</span>
                            </label>
                            <input
                                type="text"
                                value="{{ $elemento->codigo_ec }}"
                                class="input input-bordered w-full border-gray-300"
                                style="background-color: #fff !important; color: #111 !important;"
                                disabled
                            />
                            <label class="label">
                                <span class="label-text-alt">El código EC no se puede modificar</span>
                            </label>
                        </div>

                        <!-- Título -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold text-black">Título del Elemento <span class="text-error">*</span></span>
                            </label>
                            <input
                                type="text"
                                name="titulo"
                                value="{{ old('titulo', $elemento->titulo) }}"
                                placeholder="Título del elemento"
                                class="input input-bordered w-full bg-white text-black border-gray-300 @error('titulo') border-red-500 @enderror"
                                required
                            />
                            @error('titulo')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold text-black">Descripción <span class="text-error">*</span></span>
                            </label>
                            <textarea
                                name="descripcion"
                                rows="4"
                                placeholder="Descripción del elemento..."
                                class="textarea textarea-bordered w-full bg-white text-black border-gray-300 @error('descripcion') border-red-500 @enderror"
                                required
                            >{{ old('descripcion', $elemento->descripcion) }}</textarea>
                            @error('descripcion')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Tipo -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold text-black">Tipo de Elemento <span class="text-error">*</span></span>
                            </label>
                            <select
                                name="tipo"
                                class="select select-bordered w-full bg-white text-black border-gray-300 @error('tipo') border-red-500 @enderror"
                                required
                            >
                                <option value="DOCUMENTO" {{ old('tipo', $elemento->tipo) == 'DOCUMENTO' ? 'selected' : '' }}>
                                    Documento
                                </option>
                                <option value="CODIGO" {{ old('tipo', $elemento->tipo) == 'CODIGO' ? 'selected' : '' }}>
                                    Código Fuente
                                </option>
                                <option value="SCRIPT_BD" {{ old('tipo', $elemento->tipo) == 'SCRIPT_BD' ? 'selected' : '' }}>
                                    Script de Base de Datos
                                </option>
                                <option value="CONFIGURACION" {{ old('tipo', $elemento->tipo) == 'CONFIGURACION' ? 'selected' : '' }}>
                                    Configuración
                                </option>
                                <option value="OTRO" {{ old('tipo', $elemento->tipo) == 'OTRO' ? 'selected' : '' }}>
                                    Otro
                                </option>
                            </select>
                            @error('tipo')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold text-black">Estado <span class="text-error">*</span></span>
                            </label>
                            <select
                                name="estado"
                                class="select select-bordered w-full bg-white text-black border-gray-300 @error('estado') select-error @enderror"
                                required
                            >
                                <option value="PENDIENTE" {{ old('estado', $elemento->estado) == 'PENDIENTE' ? 'selected' : '' }}>
                                    Pendiente
                                </option>
                                <option value="BORRADOR" {{ old('estado', $elemento->estado) == 'BORRADOR' ? 'selected' : '' }}>
                                    Borrador
                                </option>
                                <option value="EN_REVISION" {{ old('estado', $elemento->estado) == 'EN_REVISION' ? 'selected' : '' }}>
                                    En Revisión
                                </option>
                                <option value="APROBADO" {{ old('estado', $elemento->estado) == 'APROBADO' ? 'selected' : '' }}>
                                    Aprobado
                                </option>
                                <option value="LIBERADO" {{ old('estado', $elemento->estado) == 'LIBERADO' ? 'selected' : '' }}>
                                    Liberado
                                </option>
                                <option value="OBSOLETO" {{ old('estado', $elemento->estado) == 'OBSOLETO' ? 'selected' : '' }}>
                                    Obsoleto
                                </option>
                            </select>
                            @error('estado')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Información adicional -->
                        <div class="alert alert-info mb-6 bg-blue-500 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="font-bold text-white">ℹ️ Edición de metadatos</h3>
                                <div class="text-xs text-white">
                                    <p>Esta vista solo permite editar información básica del elemento (título, descripción, tipo, estado).</p>
                                    <p class="mt-1"><strong>Para crear una nueva versión aprobada</strong>, ve a la sección de revisión:</p>
                                    <a href="{{ route('proyectos.elementos.review', [$proyecto, $elemento]) }}" class="underline hover:no-underline">
                                        → Ir a Revisar/Aprobar EC
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Información de versión (solo lectura) -->
                        <div class="stats shadow mb-6 w-full">
                            <div class="stat bg-white text-black border border-gray-200">
                                <div class="stat-title text-black">Versión Actual</div>
                                <div class="stat-value text-blue-600">v{{ $elemento->versionActual?->version ?? '1.0' }}</div>
                            </div>
                            <div class="stat bg-white text-black border border-gray-200">
                                <div class="stat-title text-black">Total Versiones</div>
                                <div class="stat-value text-blue-600">{{ $elemento->versiones->count() }}</div>
                            </div>
                            <div class="stat bg-white text-black border border-gray-200">
                                <div class="stat-title text-black">Última Modificación</div>
                                <div class="stat-value text-black text-sm">{{ $elemento->actualizado_en?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="card-actions justify-end pt-4 border-t">
                            <a href="{{ route('proyectos.elementos.index', $proyecto) }}" class="btn bg-white text-black border border-gray-300 hover:bg-gray-100">
                                Cancelar
                            </a>
                            <button type="submit" class="btn bg-black text-white gap-2 hover:bg-gray-800 border-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
