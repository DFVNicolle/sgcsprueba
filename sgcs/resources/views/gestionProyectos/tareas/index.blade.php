<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Cronograma del Proyecto
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $proyecto->nombre }} - Metodología: <span class="font-semibold">{{ $metodologia->nombre }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-ghost btn-sm">
                    ← Volver al proyecto
                </a>
                <button onclick="modalCrearTarea.showModal()" class="btn btn-sm bg-black text-white hover:bg-gray-800">
                    + Nueva Tarea
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Estilo para botones de vista -->
            <style>
                .btn-switch {
                    background: #f3f4f6;
                    color: #222;
                    border: 1px solid #d1d5db;
                    font-weight: 600;
                    padding: 0.5rem 1.2rem;
                    border-radius: 0.5rem;
                    transition: all 0.2s;
                    box-shadow: none;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                .btn-switch.active {
                    background: #2563eb;
                    color: #fff;
                    border-color: #2563eb;
                    box-shadow: 0 2px 8px #2563eb22;
                }
                .btn-switch:not(.active):hover {
                    background: #e5e7eb;
                    color: #222;
                    border-color: #2563eb;
                }
            </style>

            <!-- Selector de vista: Kanban / Gantt -->
            <div class="mb-4 flex justify-center gap-2">
                <button id="btnVistaKanban" onclick="cambiarVista('kanban')" class="btn-switch">
                    <span>📋</span> Vista Kanban
                </button>
                <button id="btnVistaGantt" onclick="cambiarVista('gantt')" class="btn-switch">
                    <span>📅</span> Vista Gantt
                </button>
            </div>

            <!-- Tablero Kanban -->
            <div class="flex gap-4 overflow-x-auto pb-4" style="min-height: 70vh;">
                @foreach($fases as $fase)
                    <div class="kanban-column flex-shrink-0" style="width: 320px;" data-fase-id="{{ $fase->id_fase }}">
                        <!-- Header de la columna -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-t-lg p-4">
                            <h3 class="font-bold text-lg">{{ $fase->nombre_fase }}</h3>
                            <p class="text-xs text-blue-100 mt-1">{{ $fase->descripcion }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                @php
                                    $faseTasksCollection = $tareas->get($fase->id_fase, collect());
                                    $totalPoints = $faseTasksCollection->sum('story_points');
                                @endphp
                                <span class="badge badge-sm bg-white text-blue-600">
                                    {{ $faseTasksCollection->count() }} tareas
                                </span>
                                @if($totalPoints > 0)
                                    <span class="badge badge-sm bg-blue-100 text-blue-700">
                                        {{ $totalPoints }} pts
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Lista de tareas (drop zone) -->
                        <div class="kanban-tasks bg-gray-100 rounded-b-lg p-3 space-y-3" style="min-height: 400px;">
                            @forelse($tareas->get($fase->id_fase, collect()) as $tarea)
                                <div class="kanban-card bg-white rounded-lg shadow-sm p-4 cursor-move hover:shadow-md transition-shadow border-l-4 {{ $tarea->id_ec ? 'border-green-500' : 'border-gray-300' }}"
                                     draggable="true"
                                     data-tarea-id="{{ $tarea->id_tarea }}"
                                     onclick="verDetallesTarea({{ $tarea->id_tarea }})">

                                    <!-- Título y prioridad -->
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-semibold text-sm text-gray-800 flex-1">
                                            {{ $tarea->nombre }}
                                        </h4>
                                        @if($tarea->prioridad > 0)
                                            <span class="badge badge-xs {{ $tarea->prioridad >= 3 ? 'badge-error' : 'badge-warning' }}">
                                                P{{ $tarea->prioridad }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Descripción (truncada) -->
                                    @if($tarea->descripcion)
                                        <p class="text-xs text-gray-600 mb-3 line-clamp-2">
                                            {{ Str::limit($tarea->descripcion, 80) }}
                                        </p>
                                    @endif

                                    <!-- EC asociado -->
                                    @if($tarea->elementoConfiguracion)
                                        <div class="flex items-center gap-1 text-xs text-green-700 bg-green-50 px-2 py-1 rounded mb-2">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="font-mono">{{ $tarea->elementoConfiguracion->codigo_ec }}</span>
                                        </div>
                                    @endif

                                    <!-- Footer con metadatos -->
                                    <div class="flex items-center justify-between text-xs text-gray-500 mt-3 pt-2 border-t">
                                        <div class="flex items-center gap-2">
                                            @if($tarea->story_points)
                                                <span class="badge badge-ghost badge-xs">{{ $tarea->story_points }} pts</span>
                                            @endif
                                            @if($tarea->sprint)
                                                <span class="badge badge-ghost badge-xs">Sprint {{ $tarea->sprint }}</span>
                                            @endif
                                        </div>
                                        @if($tarea->responsableUsuario)
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-6">
                                                    <span class="text-xs">{{ strtoupper(substr($tarea->responsableUsuario->nombre_completo, 0, 2)) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-400 py-8">
                                    <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="text-sm">No hay tareas</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Vista Gantt (Cronograma) -->
            <div id="vistaGantt" style="display: none;">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Diagrama de Gantt - Cronograma del Proyecto
                        </h3>

                    </div>

                    <!-- Timeline Header con Fechas -->
                    <div class="overflow-x-auto">
                        @php
                            // Calcular rango de fechas
                            $todasTareas = $tareas->flatten();
                            if ($todasTareas->isNotEmpty()) {
                                $fechaMin = $todasTareas->min('fecha_inicio');
                                $fechaMax = $todasTareas->max('fecha_fin');
                                $diasTotales = \Carbon\Carbon::parse($fechaMin)->diffInDays(\Carbon\Carbon::parse($fechaMax)) + 1;
                            } else {
                                $fechaMin = now();
                                $fechaMax = now()->addDays(30);
                                $diasTotales = 30;
                            }
                        @endphp

                        <div class="min-w-max">
                            <!-- Header con fechas -->
                            <div class="flex border-b border-gray-200 pb-2 mb-4">
                                <div class="w-64 font-semibold text-gray-900">Tarea</div>
                                <div class="flex-1 flex items-center justify-between text-xs text-gray-900 px-4">
                                    <span>{{ \Carbon\Carbon::parse($fechaMin)->format('d/m/Y') }}</span>
                                    <span>{{ \Carbon\Carbon::parse($fechaMax)->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            <!-- Tareas agrupadas por fase -->
                            @foreach($fases as $fase)
                                @php
                                    $tareasEnFase = $tareas->get($fase->id_fase, collect());
                                @endphp

                                @if($tareasEnFase->isNotEmpty())
                                    <!-- Título de la fase -->
                                    <div class="bg-blue-50 border-l-4 border-blue-500 px-3 py-2 mb-2 font-semibold text-sm text-blue-900">
                                        {{ $fase->nombre_fase }} ({{ $tareasEnFase->count() }} tareas)
                                    </div>

                                    @foreach($tareasEnFase as $tarea)
                                        @php
                                            $inicio = \Carbon\Carbon::parse($tarea->fecha_inicio);
                                            $fin = \Carbon\Carbon::parse($tarea->fecha_fin);
                                            $diasDesdeInicio = \Carbon\Carbon::parse($fechaMin)->diffInDays($inicio);
                                            $duracion = $inicio->diffInDays($fin) + 1;
                                            $porcentajeInicio = ($diasDesdeInicio / $diasTotales) * 100;
                                            $porcentajeDuracion = ($duracion / $diasTotales) * 100;

                                            // Color según prioridad
                                            $colorBarra = match($tarea->prioridad) {
                                                3 => 'bg-red-500',
                                                2 => 'bg-orange-500',
                                                1 => 'bg-yellow-500',
                                                default => 'bg-green-500'
                                            };
                                        @endphp

                                        <div class="flex items-center py-2 border-b border-gray-100 hover:bg-gray-50">
                                            <!-- Nombre de la tarea -->
                                            <div class="w-64 px-2">
                                                <p class="text-sm font-medium text-gray-900">{{ $tarea->nombre }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $tarea->story_points }} pts • {{ $inicio->format('d/m') }} - {{ $fin->format('d/m') }}
                                                </p>
                                            </div>

                                            <!-- Barra de Gantt -->
                                            <div class="flex-1 px-4 relative" style="height: 40px;">
                                                <div class="absolute top-1/2 transform -translate-y-1/2 h-6 {{ $colorBarra }} rounded shadow-sm flex items-center justify-center text-white text-xs font-semibold"
                                                     style="left: {{ $porcentajeInicio }}%; width: {{ max($porcentajeDuracion, 2) }}%;"
                                                     title="{{ $tarea->nombre }} ({{ $duracion }} días)">
                                                    @if($porcentajeDuracion > 5)
                                                        {{ $duracion }}d
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach

                            @if($todasTareas->isEmpty())
                                <div class="text-center text-gray-400 py-12">
                                    <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm">No hay tareas con fechas asignadas para mostrar en el cronograma</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Leyenda de prioridades -->
                    <div class="mt-6 flex items-center gap-4 text-xs">
                        <span class="font-semibold text-gray-700">Prioridad:</span>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 bg-red-500 rounded"></div>
                            <span class="text-gray-600">Crítica</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 bg-orange-500 rounded"></div>
                            <span class="text-gray-600">Alta</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                            <span class="text-gray-600">Media</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 bg-green-500 rounded"></div>
                            <span class="text-gray-600">Baja</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal: Crear Tarea -->
    <dialog id="modalCrearTarea" class="modal">
        <div class="modal-box max-w-2xl bg-white">
            <h3 class="font-bold text-lg text-black mb-4">Nueva Tarea</h3>
            <form method="POST" action="{{ route('proyectos.tareas.store', $proyecto) }}">
                @csrf

                <!-- Nombre -->
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text text-black font-semibold">Nombre de la tarea *</span></label>
                    <input type="text" name="nombre" required class="input input-bordered w-full bg-white text-black" placeholder="Ej: Implementar login de usuarios">
                </div>

                <!-- Descripción -->
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text text-black font-semibold">Descripción</span></label>
                    <textarea name="descripcion" rows="3" class="textarea textarea-bordered w-full bg-white text-black" placeholder="Como usuario, quiero poder..."></textarea>
                </div>

                <!-- Fase y EC -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-black font-semibold">Fase *</span></label>
                        <select name="id_fase" required class="select select-bordered w-full bg-white text-black">
                            @foreach($fases as $fase)
                                <option value="{{ $fase->id_fase }}">{{ $fase->nombre_fase }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-black font-semibold">Elemento de Configuración</span></label>
                        <select name="id_ec" class="select select-bordered w-full bg-white text-black">
                            <option value="">Sin EC asociado</option>
                            @foreach($elementosConfiguracion as $ec)
                                <option value="{{ $ec->id }}">{{ $ec->codigo_ec }} - {{ $ec->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Responsable y Fechas -->
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-black font-semibold">Responsable</span></label>
                        <select name="responsable" class="select select-bordered w-full bg-white text-black">
                            <option value="">Sin asignar</option>
                            @foreach($miembrosEquipo as $miembro)
                                <option value="{{ $miembro->id }}">{{ $miembro->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-black font-semibold">Fecha inicio</span></label>
                        <input type="date" name="fecha_inicio" class="input input-bordered w-full bg-white text-black">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-black font-semibold">Fecha fin</span></label>
                        <input type="date" name="fecha_fin" class="input input-bordered w-full bg-white text-black">
                    </div>
                </div>

                <!-- Story Points, Horas, Prioridad, Sprint -->
                <div class="grid grid-cols-4 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-black font-semibold">Story Points</span></label>
                        <input type="number" name="story_points" min="0" class="input input-bordered w-full bg-white text-black" placeholder="0">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-black font-semibold">Horas est.</span></label>
                        <input type="number" name="horas_estimadas" step="0.5" min="0" class="input input-bordered w-full bg-white text-black" placeholder="0">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-black font-semibold">Prioridad</span></label>
                        <select name="prioridad" class="select select-bordered w-full bg-white text-black">
                            <option value="0">Baja</option>
                            <option value="1">Media</option>
                            <option value="2">Alta</option>
                            <option value="3">Crítica</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-black font-semibold">Sprint</span></label>
                        <input type="text" name="sprint" class="input input-bordered w-full bg-white text-black" placeholder="Sprint 1">
                    </div>
                </div>

                <!-- Botones -->
                <div class="modal-action">
                    <button type="button" onclick="modalCrearTarea.close()" class="btn btn-ghost text-black">Cancelar</button>
                    <button type="submit" class="btn bg-black text-white hover:bg-gray-800">Crear Tarea</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <!-- Drag & Drop Script -->
    <script>
        let tareaArrastrada = null;
        let vistaActual = 'kanban'; // Por defecto mostrar Kanban

        // Inicializar drag & drop después de cargar el DOM
        document.addEventListener('DOMContentLoaded', function() {
            initDragAndDrop();
            cambiarVista('kanban'); // Asegurar que inicie en Kanban
        });

        // Cambiar entre vista Kanban y Gantt
        function cambiarVista(vista) {
            vistaActual = vista;
            const kanbanDiv = document.querySelector('.flex.gap-4.overflow-x-auto');
            const ganttDiv = document.getElementById('vistaGantt');
            const btnKanban = document.getElementById('btnVistaKanban');
            const btnGantt = document.getElementById('btnVistaGantt');
            const btnKanban2 = document.getElementById('btnVistaKanban2');
            const btnGantt2 = document.getElementById('btnVistaGantt2');

            if (vista === 'kanban') {
                kanbanDiv.style.display = 'flex';
                ganttDiv.style.display = 'none';
                btnKanban.classList.add('bg-blue-600', 'text-white');
                btnKanban.classList.remove('btn-outline');
                btnGantt.classList.add('btn-outline');
                btnGantt.classList.remove('bg-blue-600', 'text-white');
                btnKanban2.classList.add('bg-blue-600', 'text-white');
                btnKanban2.classList.remove('btn-outline');
                btnGantt2.classList.add('btn-outline');
                btnGantt2.classList.remove('bg-blue-600', 'text-white');
            } else {
                kanbanDiv.style.display = 'none';
                ganttDiv.style.display = 'block';
                btnGantt.classList.add('bg-blue-600', 'text-white');
                btnGantt.classList.remove('btn-outline');
                btnKanban.classList.add('btn-outline');
                btnKanban.classList.remove('bg-blue-600', 'text-white');
                btnGantt2.classList.add('bg-blue-600', 'text-white');
                btnGantt2.classList.remove('btn-outline');
                btnKanban2.classList.add('btn-outline');
                btnKanban2.classList.remove('bg-blue-600', 'text-white');
            }

            // Actualizar estado activo botones de vista
            actualizarBotonesVista();
        }

        function actualizarBotonesVista() {
            const btnKanban = document.getElementById('btnVistaKanban');
            const btnGantt = document.getElementById('btnVistaGantt');
            if (vistaActual === 'kanban') {
                btnKanban.classList.add('active');
                btnGantt.classList.remove('active');
            } else {
                btnGantt.classList.add('active');
                btnKanban.classList.remove('active');
            }
        }

        function initDragAndDrop() {
            // Drag start
            document.querySelectorAll('.kanban-card').forEach(card => {
                card.addEventListener('dragstart', function(e) {
                    tareaArrastrada = this;
                    this.style.opacity = '0.5';
                    e.dataTransfer.effectAllowed = 'move';
                });

                card.addEventListener('dragend', function(e) {
                    this.style.opacity = '1';
                });
            });

            // Drop zones
            document.querySelectorAll('.kanban-tasks').forEach(zone => {
                zone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    this.style.backgroundColor = '#EFF6FF'; // bg-blue-50
                });

                zone.addEventListener('dragleave', function(e) {
                    this.style.backgroundColor = '';
                });

                zone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.style.backgroundColor = '';

                    if (tareaArrastrada) {
                        const tareaId = tareaArrastrada.dataset.tareaId;
                        const faseId = this.closest('.kanban-column').dataset.faseId;

                        // Mover visualmente primero
                        const emptyMsg = this.querySelector('.text-center.text-gray-400');
                        if (emptyMsg) {
                            emptyMsg.remove();
                        }
                        this.appendChild(tareaArrastrada);

                        // Actualizar contador de la fase origen y destino
                        actualizarContadores();

                        // Actualizar en servidor
                        fetch(`/proyectos/{{ $proyecto->id }}/tareas/${tareaId}/cambiar-fase`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ id_fase: faseId })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                console.log('✅ Tarea movida exitosamente');
                                // Opcional: mostrar notificación
                            } else {
                                console.error('❌ Error:', data.message);
                                location.reload(); // Recargar si falla
                            }
                        })
                        .catch(err => {
                            console.error('❌ Error al mover tarea:', err);
                            location.reload(); // Recargar si falla
                        });
                    }
                });
            });
        }

        function actualizarContadores() {
            document.querySelectorAll('.kanban-column').forEach(col => {
                const tasksZone = col.querySelector('.kanban-tasks');
                const cards = tasksZone.querySelectorAll('.kanban-card');

                // Actualizar contador de tareas
                const badgeTareas = col.querySelector('.badge.bg-white');
                if (badgeTareas) {
                    badgeTareas.textContent = `${cards.length} tareas`;
                }

                // Calcular y actualizar story points
                let totalPoints = 0;
                cards.forEach(card => {
                    const pointsText = card.querySelector('.badge.badge-sm')?.textContent;
                    if (pointsText) {
                        const points = parseInt(pointsText.replace(' pts', ''));
                        if (!isNaN(points)) {
                            totalPoints += points;
                        }
                    }
                });

                // Actualizar o crear badge de story points
                let badgePoints = col.querySelector('.badge.bg-blue-100');
                if (totalPoints > 0) {
                    if (!badgePoints) {
                        // Crear el badge si no existe
                        const badgeContainer = col.querySelector('.mt-2.flex.items-center.gap-2');
                        badgePoints = document.createElement('span');
                        badgePoints.className = 'badge badge-sm bg-blue-100 text-blue-700';
                        badgeContainer.appendChild(badgePoints);
                    }
                    badgePoints.textContent = `${totalPoints} pts`;
                    badgePoints.style.display = '';
                } else if (badgePoints) {
                    // Ocultar si no hay puntos
                    badgePoints.style.display = 'none';
                }
            });
        }

        // Ver detalles de tarea
        function verDetallesTarea(tareaId) {
            console.log('Ver tarea:', tareaId);
            // TODO: Implementar modal con detalles
        }
    </script>

    @if(session('success'))
        <script>
            setTimeout(() => {
                alert('{{ session('success') }}');
            }, 100);
        </script>
    @endif
</x-app-layout>
