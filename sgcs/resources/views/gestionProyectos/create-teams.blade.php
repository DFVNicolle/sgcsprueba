<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Header -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Crear Nuevo Proyecto</h2>
                        <p class="mt-1 text-sm text-gray-600">Paso 2 de 3: Crear Equipos del Proyecto</p>
                    </div>

                    <!-- Progress Stepper -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between">
                            <!-- Step 1 - Completed -->
                            <div class="flex items-center flex-1">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-green-600">Paso 1</div>
                                    <div class="text-xs text-gray-500">Datos del Proyecto</div>
                                </div>
                            </div>
                            <div class="flex-1 h-1 bg-green-500 mx-2"></div>

                            <!-- Step 2 - Current -->
                            <div class="flex items-center flex-1">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-black text-white">
                                    <span class="text-lg font-semibold">2</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-black">Paso 2</div>
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
                                    <div class="text-xs text-gray-400">Asignar Miembros</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alert -->
                    <div class="alert alert-warning mb-6 bg-yellow-50 border-yellow-200">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="text-gray-900">
                            <strong>Importante:</strong> Debes crear al menos un equipo. Puedes crear múltiples equipos para organizar mejor tu proyecto.
                        </span>
                    </div>

                    <!-- Project Info Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Proyecto: {{ $proyectoData['nombre'] ?? 'Proyecto' }}</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Código:</span>
                                <span class="text-gray-900 ml-2">{{ $proyectoData['codigo'] ?? 'Sin código' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Metodología:</span>
                                <span class="text-gray-900 ml-2 capitalize">{{ $proyectoData['metodologia'] ?? 'No especificada' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('proyectos.store-step2') }}" id="createTeamsForm">
                        @csrf

                        <div class="space-y-6">

                            <!-- Add Team Button -->
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Equipos del Proyecto</h3>
                                <button
                                    type="button"
                                    onclick="addTeam()"
                                    class="btn bg-black text-white hover:bg-gray-800 border-black border"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Agregar Equipo
                                </button>
                            </div>

                            <!-- Teams Container -->
                            <div id="teamsContainer" class="space-y-4">
                                <!-- Teams will be added here dynamically -->
                            </div>

                            <!-- Empty State -->
                            <div id="emptyState" class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No hay equipos agregados</p>
                                <p class="text-xs text-gray-400">Haz clic en "Agregar Equipo" para comenzar</p>
                            </div>

                            @error('equipos')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                            <a
                                href="{{ route('proyectos.cancel') }}"
                                class="btn btn-ghost text-gray-700 hover:bg-gray-100"
                                onclick="return confirm('¿Estás seguro? Se perderán todos los datos ingresados.')"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar Proceso
                            </a>
                            <button
                                type="submit"
                                class="btn bg-black text-white hover:bg-gray-800 border-black border"
                                id="submitBtn"
                                disabled
                            >
                                Continuar a Paso 3
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let teamCount = 0;

        // Add first team automatically on page load
        document.addEventListener('DOMContentLoaded', function() {
            addTeam();
        });

        function addTeam() {
            teamCount++;
            const container = document.getElementById('teamsContainer');
            const emptyState = document.getElementById('emptyState');

            const teamHtml = `
                <div class="team-card border border-gray-200 rounded-lg p-6 bg-gray-50" id="team-${teamCount}">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-black">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Equipo ${teamCount}</h4>
                        </div>
                        <button
                            type="button"
                            onclick="removeTeam(${teamCount})"
                            class="text-red-600 hover:bg-red-50 p-2 rounded-md transition"
                            title="Eliminar equipo"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Nombre del Equipo -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-gray-900 font-medium">Nombre del Equipo <span class="text-red-600">*</span></span>
                            </label>
                            <input
                                type="text"
                                name="equipos[${teamCount - 1}][nombre]"
                                placeholder="Ej: Equipo Frontend, Equipo Backend..."
                                required
                                class="input input-bordered w-full bg-white text-gray-900 border-blue-500 focus:border-blue-700 focus:ring-blue-200"
                            />
                        </div>

                        <!-- Descripción (Opcional) -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-gray-900 font-medium">Descripción (Opcional)</span>
                            </label>
                            <textarea
                                name="equipos[${teamCount - 1}][descripcion]"
                                rows="2"
                                placeholder="Describe las responsabilidades de este equipo..."
                                class="textarea textarea-bordered w-full bg-white text-gray-900 border-blue-500 focus:border-blue-700 focus:ring-blue-200"
                            ></textarea>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', teamHtml);
            emptyState.classList.add('hidden');
            updateSubmitButton();
        }

        // Enable/disable submit button based on number of teams
        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            const teamCards = document.querySelectorAll('.team-card');
            if (!submitBtn) return;

            if (teamCards.length === 0) {
                submitBtn.disabled = true;
                submitBtn.classList.add('btn-disabled', 'opacity-50');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-disabled', 'opacity-50');
            }
        }

        function removeTeam(id) {
            if (!confirm('¿Estás seguro de eliminar este equipo?')) return;

            const el = document.getElementById(`team-${id}`);
            if (el) el.remove();

            const teamCards = document.querySelectorAll('.team-card');
            const emptyState = document.getElementById('emptyState');
            if (teamCards.length === 0 && emptyState) {
                emptyState.classList.remove('hidden');
            }

            updateSubmitButton();
        }

        // Form validation
        document.getElementById('createTeamsForm').addEventListener('submit', function(e) {
            const teamCards = document.querySelectorAll('.team-card');

            if (teamCards.length === 0) {
                e.preventDefault();
                alert('Debes agregar al menos un equipo.');
                return false;
            }

            if (!confirm(`¿Continuar con ${teamCards.length} equipo(s)?`)) {
                e.preventDefault();
                return false;
            }
        });
    </script>
    @endpush
</x-app-layout>
