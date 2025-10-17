<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-6">
                <div class="text-sm breadcrumbs">
                    <ul class="text-gray-600">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a></li>
                        <li class="text-gray-900 font-medium">Nuevo Proyecto</li>
                    </ul>
                </div>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Proyecto</h1>
                <p class="mt-2 text-gray-600">Paso 1 de 3: Información básica del proyecto</p>
            </div>

            <!-- Progress Stepper -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <!-- Step 1 - Current -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-black text-white">
                            <span class="text-lg font-semibold">1</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-black">Paso 1</div>
                            <div class="text-xs text-gray-500">Datos del Proyecto</div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-black mx-2"></div>

                    <!-- Step 2 - Pending -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600">
                            <span class="text-lg font-semibold">2</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-400">Paso 2</div>
                            <div class="text-xs text-gray-500">Crear Equipos</div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-gray-300 mx-2"></div>

                    <!-- Step 3 - Pending -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600">
                            <span class="text-lg font-semibold">3</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-400">Paso 3</div>
                            <div class="text-xs text-gray-500">Asignar Miembros</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if(session('error'))
                <div class="alert alert-error mb-6 bg-red-50 border border-red-200 text-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Form Card -->
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    <form action="{{ route('proyectos.store-step1') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Código del Proyecto -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-gray-900 font-medium">Código del Proyecto <span class="text-gray-500">(Opcional)</span></span>
                            </label>
                            <input
                                type="text"
                                name="codigo"
                                value="{{ old('codigo') }}"
                                placeholder="Ej: PROJ-2024-001"
                                class="input input-bordered w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-200 @error('codigo') border-red-500 @enderror"
                            />
                            @error('codigo')
                                <label class="label">
                                    <span class="label-text-alt text-red-600">{{ $message }}</span>
                                </label>
                            @enderror
                            <label class="label">
                                <span class="label-text-alt text-gray-500">Si no se especifica, se generará automáticamente</span>
                            </label>
                        </div>

                        <!-- Nombre del Proyecto -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-gray-900 font-medium">Nombre del Proyecto <span class="text-red-600">*</span></span>
                            </label>
                            <input
                                type="text"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                placeholder="Ej: Sistema de Gestión de Inventarios"
                                required
                                class="input input-bordered w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-200 @error('nombre') border-red-500 @enderror"
                            />
                            @error('nombre')
                                <label class="label">
                                    <span class="label-text-alt text-red-600">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-gray-900 font-medium">Descripción</span>
                            </label>
                            <textarea
                                name="descripcion"
                                rows="4"
                                placeholder="Describe el objetivo y alcance del proyecto..."
                                class="textarea textarea-bordered w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-200 @error('descripcion') border-red-500 @enderror"
                            >{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <label class="label">
                                    <span class="label-text-alt text-red-600">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Metodología -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-gray-900 font-medium">Metodología <span class="text-red-600">*</span></span>
                            </label>
                            <select
                                name="id_metodologia"
                                required
                                class="select select-bordered w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-200 @error('id_metodologia') border-red-500 @enderror"
                            >
                                <option value="" disabled {{ old('id_metodologia') ? '' : 'selected' }}>Selecciona una metodología</option>
                                @foreach(\App\Models\Metodologia::orderBy('nombre')->get() as $metodologia)
                                    <option value="{{ $metodologia->id_metodologia }}" {{ old('id_metodologia') == $metodologia->id_metodologia ? 'selected' : '' }}>
                                        {{ $metodologia->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_metodologia')
                                <label class="label">
                                    <span class="label-text-alt text-red-600">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a
                                href="{{ route('dashboard') }}"
                                class="btn btn-ghost text-gray-700 hover:bg-gray-100"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Cancelar
                            </a>
                            <button
                                type="submit"
                                class="btn bg-black text-white hover:bg-gray-800 border-0"
                            >
                                Continuar al Paso 2
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
