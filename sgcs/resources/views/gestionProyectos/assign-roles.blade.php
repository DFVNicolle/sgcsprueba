<x-app-layout>
    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-6">
                <div class="text-sm breadcrumbs">
                    <ul class="text-gray-600">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a></li>
                        <li><a href="{{ route('proyectos.create') }}" class="hover:text-gray-900">Nuevo Proyecto</a></li>
                        <li class="text-gray-900 font-medium">Asignar Roles</li>
                    </ul>
                </div>
            </div>
                                                        <input
                                                            type="text"
                                                            id="globalSearch"
                                                            placeholder="erick@ejemplo.com o Erick Yoel..."
                                                            class="input input-bordered w-full pl-10 bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                                            onkeydown="if(event.key === 'Enter') event.preventDefault();"
                                                            autocomplete="off"
                                                        />
            <div class="mb-8">
                <ul class="steps steps-horizontal w-full">
                    <li class="step step-primary">Información del Proyecto</li>
                    <li class="step step-primary">Asignar Roles y Miembros</li>
                </ul>
            </div>

            <!-- Warning Alert -->
            <div class="alert bg-yellow-50 border border-yellow-200 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span class="text-yellow-800">
                    <strong>Importante:</strong> Si sales de esta página sin guardar, se cancelará el proceso y deberás iniciar nuevamente.
                </span>
            </div>

            <!-- Form Card -->
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    <form action="{{ route('proyectos.store') }}" method="POST" id="assignRolesForm">
                        @csrf

                        <div class="space-y-6">
                            <!-- Search and Add Member Section -->
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">Miembros del Proyecto</h3>

                                <!-- Search Input with Modal Style -->
                                <div class="mb-6">
                                    <label class="label">
                                        <span class="label-text text-gray-900 font-medium">Buscar Usuario por Correo o Nombre</span>
                                    </label>
                                    <div class="relative">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                            </span>
                                            <input
                                                type="text"
                                                id="globalSearch"
                                                placeholder="erick@ejemplo.com o Erick Yoel..."
                                                            class="input input-bordered w-full pl-10 bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                                onkeydown="if(event.key === 'Enter') event.preventDefault();"
                                                autocomplete="off"
                                            />
                                        </div>

                                        <!-- Dropdown List (like the image) -->
                                        <div id="globalUsuarioList" class="hidden absolute z-50 mt-2 w-full bg-white border border-gray-300 rounded-lg shadow-2xl max-h-80 overflow-auto">
                                            <div class="py-2">
                                                <div id="usuarioOptions">
                                                    <!-- Options will be populated here -->
                                                </div>
                                                <div id="noResults" class="hidden px-4 py-3 text-sm text-gray-500 text-center">
                                                    No se encontraron usuarios
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Members List -->
                            <div id="selectedMembersList" class="space-y-3">
                                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Miembros Agregados</h4>
                                <div id="membersContainer" class="space-y-3">
                                    <!-- Selected members will appear here -->
                                </div>
                                <div id="emptyState" class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No hay miembros agregados</p>
                                    <p class="text-xs text-gray-400">Busca y selecciona usuarios arriba</p>
                                </div>
                            </div>

                            @error('miembros')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>                        <!-- Actions -->
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
                                class="btn bg-black text-white hover:bg-gray-800 border-0"
                                id="submitBtn"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Crear Proyecto
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        const usuarios = @json($usuarios);
        const roles = @json($roles);
        let selectedMembers = [];
        let memberIdCounter = 0;

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔍 Usuarios cargados:', usuarios.length);
            console.log('📋 Roles cargados:', roles.length);
            console.log('👥 Lista de usuarios:', usuarios);

            renderUsuarioOptions();
            updateMembersList();

            // Add event listener to search input
            const searchInput = document.getElementById('globalSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    console.log('⌨️ Escribiendo:', this.value);
                    filterGlobalUsuarios();
                });
                searchInput.addEventListener('focus', function() {
                    console.log('👁️ Focus en búsqueda');
                    showGlobalList();
                });
            } else {
                console.error('❌ No se encontró el input globalSearch');
            }
        });

        // Render all usuario options in the dropdown
        function renderUsuarioOptions() {
            const container = document.getElementById('usuarioOptions');
            if (!container) {
                console.error('❌ No se encontró usuarioOptions');
                return;
            }

            if (usuarios.length === 0) {
                console.warn('⚠️ No hay usuarios para mostrar');
                container.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">No hay usuarios disponibles</div>';
                return;
            }

            container.innerHTML = usuarios.map(u => `
                <div class="usuario-option px-4 py-2 hover:bg-gray-100 cursor-pointer transition border-b border-gray-100 last:border-0"
                     data-id="${u.id}"
                     data-nombre="${u.nombre_completo}"
                     data-correo="${u.correo}"
                     onclick="addUserToProject('${u.id}', '${escapeHtml(u.nombre_completo)}', '${escapeHtml(u.correo)}')">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-black flex items-center justify-center">
                                            <span class="text-white font-semibold text-sm">${u.nombre_completo.charAt(0).toUpperCase()}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${u.nombre_completo}</div>
                            <div class="text-sm text-gray-500 truncate">${u.correo}</div>
                        </div>
                    </div>
                </div>
            `).join('');

            console.log('✅ Opciones renderizadas:', usuarios.length);
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Filter usuarios based on search
        function filterGlobalUsuarios() {
            const searchTerm = document.getElementById('globalSearch').value.toLowerCase().trim();
            const options = document.querySelectorAll('.usuario-option');
            const noResults = document.getElementById('noResults');
            let hasResults = false;

            console.log('🔎 Buscando:', searchTerm);
            console.log('📝 Opciones encontradas:', options.length);

            if (searchTerm === '') {
                options.forEach(option => option.classList.remove('hidden'));
                noResults.classList.add('hidden');
                console.log('✅ Mostrando todas las opciones');
                return;
            }

            options.forEach(option => {
                const nombre = option.dataset.nombre.toLowerCase();
                const correo = option.dataset.correo.toLowerCase();

                if (nombre.includes(searchTerm) || correo.includes(searchTerm)) {
                    option.classList.remove('hidden');
                    hasResults = true;
                    console.log('✅ Coincide:', nombre, correo);
                } else {
                    option.classList.add('hidden');
                }
            });

            if (hasResults) {
                noResults.classList.add('hidden');
                console.log('✅ Se encontraron resultados');
            } else {
                noResults.classList.remove('hidden');
                console.log('❌ No se encontraron resultados');
            }
        }

        // Show dropdown list
        function showGlobalList() {
            const dropdown = document.getElementById('globalUsuarioList');
            dropdown.classList.remove('hidden');
            console.log('👁️ Dropdown mostrado');
        }

        // Add user to project
        function addUserToProject(userId, nombre, correo) {
            // Check if already added
            if (selectedMembers.some(m => m.userId === userId)) {
                alert('Este usuario ya fue agregado al proyecto.');
                return;
            }

            memberIdCounter++;
            const member = {
                id: memberIdCounter,
                userId: userId,
                nombre: nombre,
                correo: correo,
                rolId: ''
            };

            selectedMembers.push(member);

            // Clear search and hide dropdown
            document.getElementById('globalSearch').value = '';
            document.getElementById('globalUsuarioList').classList.add('hidden');

            // Re-render options and update list
            renderUsuarioOptions();
            updateMembersList();
        }

        // Update the members list display
        function updateMembersList() {
            const container = document.getElementById('membersContainer');
            const emptyState = document.getElementById('emptyState');

            if (selectedMembers.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                updateSubmitButton();
                return;
            }

            emptyState.classList.add('hidden');

            container.innerHTML = selectedMembers.map((member, index) => `
                <div class="member-card bg-gray-50 border border-gray-200 rounded-lg p-4" id="member-card-${member.id}">
                    <div class="flex items-start gap-4">
                        <!-- User Info -->
                        <div class="flex items-center space-x-3 flex-1">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-black flex items-center justify-center">
                                        <span class="text-white font-semibold">${member.nombre.charAt(0).toUpperCase()}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900">${member.nombre}</div>
                                <div class="text-sm text-gray-500">${member.correo}</div>
                            </div>
                        </div>

                        <!-- Role Selection -->
                        <div class="flex-shrink-0 w-48">
                            <select
                                name="miembros[${index}][rol_id]"
                                required
                                onchange="updateMemberRole(${member.id}, this.value)"
                                class="select select-sm select-bordered w-full bg-white text-gray-900 border-gray-300"
                            >
                                <option value="" ${member.rolId === '' ? 'selected' : ''}>Seleccionar rol</option>
                                ${roles.map(r => `<option value="${r.id}" ${member.rolId == r.id ? 'selected' : ''}>${r.nombre}</option>`).join('')}
                            </select>
                        </div>

                        <!-- Hidden input for usuario_id -->
                        <input type="hidden" name="miembros[${index}][usuario_id]" value="${member.userId}" />

                        <!-- Remove Button -->
                        <button
                            type="button"
                            onclick="removeMember(${member.id})"
                            class="flex-shrink-0 p-2 text-red-600 hover:bg-red-50 rounded-md transition"
                            title="Eliminar"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `).join('');

            updateSubmitButton();
        }

        // Update member role
        function updateMemberRole(memberId, rolId) {
            const member = selectedMembers.find(m => m.id === memberId);
            if (member) {
                member.rolId = rolId;
            }
        }

        // Update member role
        function updateMemberRole(memberId, rolId) {
            const member = selectedMembers.find(m => m.id === memberId);
            if (member) {
                member.rolId = rolId;
            }
        }

        // Remove member
        function removeMember(memberId) {
            selectedMembers = selectedMembers.filter(m => m.id !== memberId);
            updateMembersList();
        }

        // Update submit button state
        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');

            if (selectedMembers.length === 0) {
                submitBtn.disabled = true;
                submitBtn.classList.add('btn-disabled', 'opacity-50');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-disabled', 'opacity-50');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const searchInput = document.getElementById('globalSearch');
            const dropdown = document.getElementById('globalUsuarioList');

            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Form validation before submit
        document.getElementById('assignRolesForm').addEventListener('submit', function(e) {
            if (selectedMembers.length === 0) {
                e.preventDefault();
                alert('Debes agregar al menos un miembro al proyecto.');
                return false;
            }

            // Check if all members have roles
            const membersWithoutRole = selectedMembers.filter(m => !m.rolId || m.rolId === '');
            if (membersWithoutRole.length > 0) {
                e.preventDefault();
                alert('Todos los miembros deben tener un rol asignado.');
                return false;
            }

            // Confirm before saving
            if (!confirm(`¿Estás seguro de crear el proyecto con ${selectedMembers.length} miembro(s)?`)) {
                e.preventDefault();
                return false;
            }
        });
    </script>
    @endpush
</x-app-layout>
