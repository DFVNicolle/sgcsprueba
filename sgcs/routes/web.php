

<?php

use App\Http\Controllers\PerfilController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestionProyectos\ProyectoController;
use App\Http\Controllers\GestionProyectos\ElementoConfiguracionController;
use App\Http\Controllers\GestionProyectos\RelacionECController;
use Illuminate\Support\Facades\Route;

// Rutas para verificación 2FA en login
Route::get('/2fa', [\App\Http\Controllers\Auth\TwoFactorController::class, 'showForm'])->name('auth.2fa');
Route::post('/2fa', [\App\Http\Controllers\Auth\TwoFactorController::class, 'verify'])->name('auth.2fa.verify');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('profile.edit');
    Route::patch('/perfil', [PerfilController::class, 'update'])->name('profile.update');
    Route::delete('/perfil', [PerfilController::class, 'destroy'])->name('profile.destroy');

    // 2FA
    Route::post('/perfil/activar-2fa', [PerfilController::class, 'activar2fa'])->name('perfil.activar2fa');
    Route::post('/perfil/confirmar-2fa', [PerfilController::class, 'confirmar2fa'])->name('perfil.confirmar2fa');
    Route::post('/perfil/desactivar-2fa', [PerfilController::class, 'desactivar2fa'])->name('perfil.desactivar2fa');

    // Rutas de Gestión de Proyectos
    Route::prefix('proyectos')->name('proyectos.')->group(function () {
        // Lista de proyectos
        Route::get('/', [ProyectoController::class, 'index'])->name('index');

        // Paso 1: Datos del Proyecto (DEBE IR ANTES de /{proyecto})
        Route::get('/crear', [ProyectoController::class, 'create'])->name('create');
        Route::post('/crear/paso-1', [ProyectoController::class, 'storeStep1'])->name('store-step1');
        Route::get('/crear/paso-1', function() {
            return redirect()->route('proyectos.create')
                ->with('info', 'Por favor completa el formulario para crear el proyecto.');
        });

        // Paso 2: Crear Equipos
        Route::get('/crear/paso-2', [ProyectoController::class, 'createTeams'])->name('create-teams');
        Route::post('/crear/paso-2', [ProyectoController::class, 'storeStep2'])->name('store-step2');

        // Paso 3: Asignar Miembros
        Route::get('/crear/paso-3', [ProyectoController::class, 'assignMembers'])->name('assign-members');

        // Guardar proyecto completo
        Route::post('/guardar', [ProyectoController::class, 'store'])->name('store');

        // Cancelar proceso
        Route::get('/cancelar', [ProyectoController::class, 'cancel'])->name('cancel');

        // Ver proyecto específico (dashboard interno) - DEBE IR AL FINAL
        Route::get('/{proyecto}', [ProyectoController::class, 'show'])->name('show');

        // Rutas de Elementos de Configuración (anidadas bajo proyecto)
        Route::prefix('/{proyecto}/elementos')->name('elementos.')->group(function () {
            Route::get('/', [ElementoConfiguracionController::class, 'index'])->name('index');
            Route::get('/grafo', [ElementoConfiguracionController::class, 'grafo'])->name('grafo');
            Route::get('/ver-grafo', [ElementoConfiguracionController::class, 'verGrafo'])->name('verGrafo');
            Route::get('/crear', [ElementoConfiguracionController::class, 'create'])->name('create');
            Route::post('/', [ElementoConfiguracionController::class, 'store'])->name('store');
            Route::get('/{elemento}/editar', [ElementoConfiguracionController::class, 'edit'])->name('edit');
            Route::put('/{elemento}', [ElementoConfiguracionController::class, 'update'])->name('update');
            Route::delete('/{elemento}', [ElementoConfiguracionController::class, 'destroy'])->name('destroy');

            // Rutas para revisión y aprobación de EC
            Route::get('/{elemento}/revisar', [ElementoConfiguracionController::class, 'review'])->name('review');
            Route::post('/{elemento}/aprobar', [ElementoConfiguracionController::class, 'approve'])->name('approve');

            // Rutas de Relaciones (anidadas bajo elemento)
            Route::prefix('/{elemento}/relaciones')->name('relaciones.')->group(function () {
                Route::get('/', [RelacionECController::class, 'index'])->name('index');
                Route::get('/crear', [RelacionECController::class, 'create'])->name('create');
                Route::post('/', [RelacionECController::class, 'store'])->name('store');
                Route::delete('/{relacion}', [RelacionECController::class, 'destroy'])->name('destroy');
            });
        });

        // Rutas de Tareas/Cronograma (anidadas bajo proyecto)
        Route::prefix('/{proyecto}/tareas')->name('tareas.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'store'])->name('store');
            Route::get('/{tarea}', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'show'])->name('show');
            Route::put('/{tarea}', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'update'])->name('update');
            Route::delete('/{tarea}', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'destroy'])->name('destroy');
            Route::post('/{tarea}/cambiar-fase', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'cambiarFase'])->name('cambiar-fase');
        });
    });
});

require __DIR__.'/auth.php';
