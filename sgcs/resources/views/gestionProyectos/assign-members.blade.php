<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Header -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Crear Nuevo Proyecto</h2>
                        <p class="mt-1 text-sm text-gray-600">Paso 3 de 3: Asignar Miembros a Equipos</p>
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

                            <!-- Step 2 - Completed -->
                            <div class="flex items-center flex-1">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-green-600">Paso 2</div>
                                    <div class="text-xs text-gray-500">Crear Equipos</div>
                                </div>
                            </div>
                            <div class="flex-1 h-1 bg-green-500 mx-2"></div>

                            <!-- Step 3 - Current -->
                            <div class="flex items-center flex-1">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-black text-white">
                                    <span class="text-lg font-semibold">3</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-black">Paso 3</div>
                                    <div class="text-xs text-gray-500">Asignar Miembros</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alert -->
                    <div class="alert alert-info mb-6 bg-blue-50 border-blue-200">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-gray-900">
                            Busca usuarios y asígnalos a los equipos. Debes agregar al menos un miembro.
                        </span>
                    </div>

                    <!-- Project & Teams Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Proyecto: {{ $proyectoData['nombre'] }}</h3>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-xs text-gray-600">Equipos creados:</span>
                            @foreach($equiposData as $equipo)
                                <span class="badge badge-sm bg-white text-black border border-gray-200">{{ $equipo['nombre'] }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('proyectos.store') }}" id="assignMembersForm">
                        @csrf

                        <div class="space-y-6">

                            <!-- Search User Section -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <div class="mb-6">
                                    <label class="label">
                                        <span class="label-text text-gray-900 font-medium">Buscar Usuario</span>
                                    </label>
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

                                    <!-- Dropdown List -->
                                    <div id="globalUsuarioList" class="hidden absolute z-50 mt-2 w-full max-w-2xl bg-white border border-gray-300 rounded-lg shadow-2xl max-h-80 overflow-auto">
                                        <div class="py-2">
                                            <div id="usuarioOptions">
                                                <!-- Options populated by JS -->
                                            </div>
                                            <div id="noResults" class="hidden px-4 py-3 text-sm text-gray-500 text-center">
                                                No se encontraron usuarios
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Select Team and Role -->
                                <div id="selectSection" class="hidden space-y-4">
                                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-12 h-12 rounded-full bg-black flex items-center justify-center">
                                                    <span class="text-white font-semibold text-lg" id="selectedUserInitial"></span>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900" id="selectedUserName"></div>
                                                    <div class="text-sm text-gray-600" id="selectedUserEmail"></div>
                                                </div>
                                            </div>
                                            <button type="button" onclick="clearSelection()" class="text-red-600 hover:bg-red-50 p-2 rounded-md">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Select Team -->
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text text-gray-900 font-medium">Asignar a Equipo <span class="text-red-600">*</span></span>
                                                </label>
                                                <select id="selectedTeam" class="select select-bordered w-full bg-white text-gray-900 border-gray-300">
                                                    <option value="">Seleccionar equipo</option>
                                                    @foreach($equiposData as $index => $equipo)
                                                        <option value="{{ $index }}">{{ $equipo['nombre'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Select Role -->
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text text-gray-900 font-medium">Rol <span class="text-red-600">*</span></span>
                                                </label>
                                                <select id="selectedRole" class="select select-bordered w-full bg-white text-gray-900 border-gray-300">
                                                    <option value="">Seleccionar rol</option>
                                                    @foreach($roles as $rol)
                                                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            onclick="addMemberToTeam()"
                                            class="btn bg-black text-white hover:bg-gray-800 border-black border w-full mt-4"
                                        >
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Agregar Miembro
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Teams with Members -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900">Miembros por Equipo</h3>

                                @foreach($equiposData as $index => $equipo)
                                    <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                                        <div class="flex items-center space-x-3 mb-4">
                                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $equipo['nombre'] }}</h4>
                                        </div>

                                        <div id="team-{{ $index }}-members" class="space-y-3">
                                            <!-- Members will be added here -->
                                        </div>

                                        <div id="team-{{ $index }}-empty" class="text-center py-6 text-gray-500 text-sm">
                                            <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                            </svg>
                                            Sin miembros asignados
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('miembros')
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
                                class="btn bg-black text-white hover:bg-gray-800 border-0"
                                id="submitBtn"
                                disabled
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
        const equipos = @json($equiposData);
        let selectedMembers = [];
        let memberIdCounter = 0;
        let currentSelectedUser = null;

        document.addEventListener('DOMContentLoaded', function() {
            renderUsuarioOptions();
            updateSubmitButton();

            const searchInput = document.getElementById('globalSearch');
            searchInput.addEventListener('input', filterGlobalUsuarios);
            searchInput.addEventListener('focus', showGlobalList);
        });

        function renderUsuarioOptions() {
            const container = document.getElementById('usuarioOptions');
            container.innerHTML = usuarios.map(u => `
                <div class="usuario-option px-4 py-2 hover:bg-gray-100 cursor-pointer transition border-b border-gray-100 last:border-0"
                     data-id="${u.id}"
                     data-nombre="${u.nombre_completo}"
                     data-correo="${u.correo}"
                     onclick="selectUser('${u.id}', '${escapeHtml(u.nombre_completo)}', '${escapeHtml(u.correo)}')">
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
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function filterGlobalUsuarios() {
            const searchTerm = document.getElementById('globalSearch').value.toLowerCase().trim();
            const options = document.querySelectorAll('.usuario-option');
            const noResults = document.getElementById('noResults');
            let hasResults = false;

            if (searchTerm === '') {
                options.forEach(option => option.classList.remove('hidden'));
                noResults.classList.add('hidden');
                return;
            }

            options.forEach(option => {
                const nombre = option.dataset.nombre.toLowerCase();
                const correo = option.dataset.correo.toLowerCase();

                if (nombre.includes(searchTerm) || correo.includes(searchTerm)) {
                    option.classList.remove('hidden');
                    hasResults = true;
                } else {
                    option.classList.add('hidden');
                }
            });

            noResults.classList.toggle('hidden', hasResults);
        }

        function showGlobalList() {
            document.getElementById('globalUsuarioList').classList.remove('hidden');
        }

        function selectUser(userId, nombre, correo) {
            // Check if already added
            if (selectedMembers.some(m => m.userId === userId)) {
                alert('Este usuario ya fue agregado a un equipo.');
                return;
            }

            currentSelectedUser = { userId, nombre, correo };

            // Show selection section
            document.getElementById('selectSection').classList.remove('hidden');
            document.getElementById('selectedUserInitial').textContent = nombre.charAt(0).toUpperCase();
            document.getElementById('selectedUserName').textContent = nombre;
            document.getElementById('selectedUserEmail').textContent = correo;

            // Clear search and hide dropdown
            document.getElementById('globalSearch').value = '';
            document.getElementById('globalUsuarioList').classList.add('hidden');

            // Reset selects
            document.getElementById('selectedTeam').value = '';
            document.getElementById('selectedRole').value = '';
        }

        function clearSelection() {
            currentSelectedUser = null;
            document.getElementById('selectSection').classList.add('hidden');
            document.getElementById('selectedTeam').value = '';
            document.getElementById('selectedRole').value = '';
        }

        function addMemberToTeam() {
            if (!currentSelectedUser) return;

            const teamIndex = document.getElementById('selectedTeam').value;
            const roleId = document.getElementById('selectedRole').value;

            if (teamIndex === '' || roleId === '') {
                alert('Debes seleccionar un equipo y un rol.');
                return;
            }

            memberIdCounter++;
            const member = {
                id: memberIdCounter,
                userId: currentSelectedUser.userId,
                nombre: currentSelectedUser.nombre,
                correo: currentSelectedUser.correo,
                teamIndex: parseInt(teamIndex),
                roleId: roleId,
                roleName: roles.find(r => r.id == roleId).nombre
            };

            selectedMembers.push(member);
            renderTeamMembers(teamIndex);
            clearSelection();
            updateSubmitButton();
        }

        function renderTeamMembers(teamIndex) {
            const container = document.getElementById(`team-${teamIndex}-members`);
            const emptyState = document.getElementById(`team-${teamIndex}-empty`);
            const teamMembers = selectedMembers.filter(m => m.teamIndex == teamIndex);

            if (teamMembers.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');

            container.innerHTML = teamMembers.map((member, index) => {
                const globalIndex = selectedMembers.indexOf(member);
                return `
                    <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3 flex-1">
                            <div class="w-10 h-10 rounded-full bg-black flex items-center justify-center">
                                <span class="text-white font-semibold">${member.nombre.charAt(0).toUpperCase()}</span>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">${member.nombre}</div>
                                <div class="text-sm text-gray-500">${member.correo}</div>
                            </div>
                            <div class="badge badge-lg bg-white text-black border border-gray-200">${member.roleName}</div>
                        </div>
                        <input type="hidden" name="miembros[${globalIndex}][usuario_id]" value="${member.userId}" />
                        <input type="hidden" name="miembros[${globalIndex}][rol_id]" value="${member.roleId}" />
                        <input type="hidden" name="miembros[${globalIndex}][equipo_index]" value="${member.teamIndex}" />
                        <button type="button" onclick="removeMember(${member.id})" class="ml-4 text-red-600 hover:bg-red-50 p-2 rounded-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
            }).join('');
        }

        function removeMember(memberId) {
            const member = selectedMembers.find(m => m.id === memberId);
            if (!member) return;

            selectedMembers = selectedMembers.filter(m => m.id !== memberId);
            renderTeamMembers(member.teamIndex);
            updateSubmitButton();
        }

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

        // Form validation
        document.getElementById('assignMembersForm').addEventListener('submit', function(e) {
            if (selectedMembers.length === 0) {
                e.preventDefault();
                alert('Debes agregar al menos un miembro al proyecto.');
                return false;
            }

            const totalTeams = equipos.length;
            const teamsWithMembers = new Set(selectedMembers.map(m => m.teamIndex)).size;

            if (!confirm(`¿Crear proyecto con ${selectedMembers.length} miembro(s) en ${teamsWithMembers} de ${totalTeams} equipo(s)?`)) {
                e.preventDefault();
                return false;
            }
        });
    </script>
    @endpush
</x-app-layout>
