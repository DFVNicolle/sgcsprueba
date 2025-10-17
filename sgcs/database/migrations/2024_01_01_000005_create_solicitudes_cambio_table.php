<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitudes_cambio', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('proyecto_id', 36);
            $table->string('titulo', 255)->nullable();
            $table->text('descripcion_cambio')->nullable();
            $table->text('motivo_cambio')->nullable();
            $table->enum('prioridad', ['BAJA', 'MEDIA', 'ALTA', 'CRITICA'])->default('MEDIA');
            $table->enum('estado', ['ABIERTA', 'EN_REVISION', 'APROBADA', 'RECHAZADA', 'IMPLEMENTADA', 'CERRADA'])->default('ABIERTA');
            $table->char('solicitante_id', 36)->nullable();
            $table->text('resumen_impacto')->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('proyecto_id')->references('id')->on('proyectos');
            $table->foreign('solicitante_id')->references('id')->on('usuarios');
        });

        Schema::create('items_cambio', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('solicitud_cambio_id', 36);
            $table->char('ec_id', 36);
            $table->char('version_actual_ec_id', 36)->nullable();
            $table->string('version_propuesta', 50)->nullable();
            $table->text('nota')->nullable();
            $table->foreign('solicitud_cambio_id')->references('id')->on('solicitudes_cambio')->onDelete('cascade');
            $table->foreign('ec_id')->references('id')->on('elementos_configuracion');
            $table->foreign('version_actual_ec_id')->references('id')->on('versiones_ec');
        });

        Schema::create('comite_cambios', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('proyecto_id', 36);
            $table->string('nombre', 255)->nullable();
            $table->integer('quorum')->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->foreign('proyecto_id')->references('id')->on('proyectos');
        });

        Schema::create('miembros_ccb', function (Blueprint $table) {
            $table->char('ccb_id', 36);
            $table->char('usuario_id', 36);
            $table->string('rol_en_ccb', 100)->nullable();
            $table->primary(['ccb_id', 'usuario_id']);
            $table->foreign('ccb_id')->references('id')->on('comite_cambios')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
        });

        Schema::create('votos_ccb', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('ccb_id', 36);
            $table->char('solicitud_cambio_id', 36);
            $table->char('usuario_id', 36);
            $table->enum('voto', ['APROBAR', 'RECHAZAR', 'ABSTENERSE']);
            $table->text('comentario')->nullable();
            $table->timestamp('votado_en')->useCurrent();
            $table->foreign('ccb_id')->references('id')->on('comite_cambios');
            $table->foreign('solicitud_cambio_id')->references('id')->on('solicitudes_cambio');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votos_ccb');
        Schema::dropIfExists('miembros_ccb');
        Schema::dropIfExists('comite_cambios');
        Schema::dropIfExists('items_cambio');
        Schema::dropIfExists('solicitudes_cambio');
    }
};
