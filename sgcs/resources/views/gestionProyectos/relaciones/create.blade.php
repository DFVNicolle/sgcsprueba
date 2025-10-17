<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Nueva Relación
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Desde: <span class="font-semibold">{{ $elemento->codigo_ec }} - {{ $elemento->titulo }}</span>
                </p>
            </div>
            <a href="{{ route('proyectos.elementos.relaciones.index', [$proyecto, $elemento]) }}" class="btn btn-ghost btn-sm">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body bg-white text-black rounded-xl border border-gray-200">
                    <form action="{{ route('proyectos.elementos.relaciones.store', [$proyecto, $elemento]) }}" method="POST">
                        @csrf

                        <!-- Tipo de Relación -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold">Tipo de Relación <span class="text-error">*</span></span>
                            </label>
                            <select
                                name="tipo_relacion"
                                class="select select-bordered w-full bg-white text-black border-gray-300 @error('tipo_relacion') select-error @enderror"
                                required
                            >
                                <option value="" disabled selected>Selecciona el tipo de relación</option>
                                <option value="DEPENDE_DE" {{ old('tipo_relacion') == 'DEPENDE_DE' ? 'selected' : '' }}>
                                    🔴 Depende de (Este elemento requiere otro elemento)
                                </option>
                                <option value="DERIVADO_DE" {{ old('tipo_relacion') == 'DERIVADO_DE' ? 'selected' : '' }}>
                                    🟡 Derivado de (Este elemento es una extensión de otro)
                                </option>
                                <option value="REFERENCIA" {{ old('tipo_relacion') == 'REFERENCIA' ? 'selected' : '' }}>
                                    🔵 Referencia a (Este elemento hace referencia a otro)
                                </option>
                                <option value="REQUERIDO_POR" {{ old('tipo_relacion') == 'REQUERIDO_POR' ? 'selected' : '' }}>
                                    🟢 Requerido por (Este elemento es requerido por otro)
                                </option>
                            </select>
                            @error('tipo_relacion')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Elemento Destino -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold">Elemento Destino <span class="text-error">*</span></span>
                            </label>
                            <select
                                name="hacia_ec"
                                class="select select-bordered w-full bg-white text-black border-gray-300 @error('hacia_ec') select-error @enderror"
                                required
                            >
                                <option value="" disabled selected>Selecciona el elemento destino</option>
                                @foreach($elementosDisponibles as $ec)
                                    <option value="{{ $ec->id }}" {{ old('hacia_ec') == $ec->id ? 'selected' : '' }}>
                                        {{ $ec->codigo_ec }} - {{ $ec->titulo }} ({{ $ec->tipo }})
                                    </option>
                                @endforeach
                            </select>
                            @error('hacia_ec')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                            @if($elementosDisponibles->isEmpty())
                                <label class="label">
                                    <span class="label-text-alt text-warning">No hay elementos disponibles para relacionar</span>
                                </label>
                            @endif
                        </div>

                        <!-- Nota -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold">Nota (Opcional)</span>
                            </label>
                            <textarea
                                name="nota"
                                rows="3"
                                placeholder="Describe la relación entre estos elementos..."
                                class="textarea textarea-bordered w-full bg-white text-black border-gray-300 @error('nota') textarea-error @enderror"
                            >{{ old('nota') }}</textarea>
                            @error('nota')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Ejemplo visual -->
                        <div class="alert bg-gray-100 text-black border border-gray-300 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm">
                                <strong>Ejemplo de relación:</strong>
                                <div class="mt-2 font-mono text-xs">
                                    <span class="font-bold">{{ $elemento->codigo_ec }}</span>
                                    <span class="mx-2">→</span>
                                    <span class="badge badge-sm">TIPO</span>
                                    <span class="mx-2">→</span>
                                    <span class="font-bold">[Elemento Destino]</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="card-actions justify-end pt-4 border-t">
                            <a href="{{ route('proyectos.elementos.relaciones.index', [$proyecto, $elemento]) }}"
                               class="btn btn-ghost">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary gap-2" {{ $elementosDisponibles->isEmpty() ? 'disabled' : '' }}>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="">Crear Relación</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
