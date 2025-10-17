<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Crear Elemento de Configuración
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Proyecto: <span class="font-semibold">{{ $proyecto->nombre }}</span>
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
                    <form action="{{ route('proyectos.elementos.store', $proyecto) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Título -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold text-black">Título del Elemento <span class="text-error">*</span></span>
                            </label>
                            <input
                                type="text"
                                name="titulo"
                                value="{{ old('titulo') }}"
                                placeholder="Ej: Documento de Requisitos, Script de Migración, etc."
                                class="input input-bordered w-full bg-white text-black border-gray-300 @error('titulo') border-red-500 @enderror"
                                required
                            />
                            @error('titulo')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                            <label class="label">
                                <span class="label-text-alt">El código EC se generará automáticamente</span>
                            </label>
                        </div>

                        <!-- Descripción -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold text-black">Descripción <span class="text-error">*</span></span>
                            </label>
                            <textarea
                                name="descripcion"
                                rows="4"
                                placeholder="Describe el propósito y contenido de este elemento de configuración..."
                                class="textarea textarea-bordered w-full bg-white text-black border-gray-300 @error('descripcion') border-red-500 @enderror"
                                required
                            >{{ old('descripcion') }}</textarea>
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
                                <option value="" disabled selected>Selecciona el tipo</option>
                                <option value="DOCUMENTO" {{ old('tipo') == 'DOCUMENTO' ? 'selected' : '' }}>
                                    📄 Documento
                                </option>
                                <option value="CODIGO" {{ old('tipo') == 'CODIGO' ? 'selected' : '' }}>
                                    💻 Código Fuente
                                </option>
                                <option value="SCRIPT_BD" {{ old('tipo') == 'SCRIPT_BD' ? 'selected' : '' }}>
                                    🗄️ Script de Base de Datos
                                </option>
                                <option value="CONFIGURACION" {{ old('tipo') == 'CONFIGURACION' ? 'selected' : '' }}>
                                    ⚙️ Configuración
                                </option>
                                <option value="OTRO" {{ old('tipo') == 'OTRO' ? 'selected' : '' }}>
                                    📦 Otro
                                </option>
                            </select>
                            @error('tipo')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Nota: no se solicita URL de commit al crear el EC. Puedes asociar commits luego desde la edición del elemento. -->

                        <!-- Información adicional -->
                        <div class="alert alert-info mb-6 bg-blue-500 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="font-bold text-white">📋 ¿Cómo funciona?</h3>
                                <div class="text-xs text-white">
                                    <ul class="list-disc list-inside mt-1 space-y-1">
                                        <li><strong>Paso 1:</strong> Crea el elemento de configuración (solo descripción, sin código)</li>
                                        <li><strong>Paso 2:</strong> Trabaja en GitHub y haz tus commits</li>
                                        <li><strong>Paso 3:</strong> Vuelve aquí, edita el elemento y vincula el commit de GitHub</li>
                                        <li>Cada vez que vincules un commit, se creará una nueva versión automáticamente</li>
                                        <li>El sistema obtendrá automáticamente los metadatos del commit (autor, fecha, mensaje)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="card-actions justify-end pt-4 border-t">
                            <a href="{{ route('proyectos.elementos.index', $proyecto) }}" class="btn btn-ghost">
                                Cancelar
                            </a>
                            <button type="submit" class="btn bg-black text-white gap-2 hover:bg-gray-800 border-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Crear Elemento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
