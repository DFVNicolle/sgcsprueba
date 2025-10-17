<?php

use App\Http\Controllers\Auth\SesionAutenticadaController;
use App\Http\Controllers\Auth\ContraseñaConfirmableController;
use App\Http\Controllers\Auth\VerificacionEmailNotificacionController;
use App\Http\Controllers\Auth\VerificacionEmailSolicitudController;
use App\Http\Controllers\Auth\NuevaContraseñaController;
use App\Http\Controllers\Auth\ContraseñaController;
use App\Http\Controllers\Auth\RestablecerContraseñaEnlaceController;
use App\Http\Controllers\Auth\UsuarioRegistradoController;
use App\Http\Controllers\Auth\VerificarEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('registro', [UsuarioRegistradoController::class, 'create'])
        ->name('register');

    Route::post('registro', [UsuarioRegistradoController::class, 'store']);

    Route::get('iniciar-sesion', [SesionAutenticadaController::class, 'create'])
        ->name('login');

    Route::post('iniciar-sesion', [SesionAutenticadaController::class, 'store']);

    Route::get('olvide-contraseña', [RestablecerContraseñaEnlaceController::class, 'create'])
        ->name('password.request');

    Route::post('olvide-contraseña', [RestablecerContraseñaEnlaceController::class, 'store'])
        ->name('password.email');

    Route::get('restablecer-contraseña/{token}', [NuevaContraseñaController::class, 'create'])
        ->name('password.reset');

    Route::post('restablecer-contraseña', [NuevaContraseñaController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verificar-email', VerificacionEmailSolicitudController::class)
        ->name('verification.notice');

    Route::get('verificar-email/{id}/{hash}', VerificarEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [VerificacionEmailNotificacionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirmar-contraseña', [ContraseñaConfirmableController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirmar-contraseña', [ContraseñaConfirmableController::class, 'store']);

    Route::put('password', [ContraseñaController::class, 'update'])->name('password.update');

    Route::post('logout', [SesionAutenticadaController::class, 'destroy'])
        ->name('logout');
});
