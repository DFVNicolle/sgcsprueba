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
        Schema::create('elementos_configuracion', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('codigo_ec', 50)->unique();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->char('proyecto_id', 36);
            $table->enum('tipo', ['DOCUMENTO', 'CODIGO', 'SCRIPT_BD', 'CONFIGURACION', 'OTRO'])->default('OTRO');
            $table->char('version_actual_id', 36)->nullable();
            $table->char('creado_por', 36)->nullable();
            $table->enum('estado', ['PENDIENTE','BORRADOR', 'EN_REVISION', 'APROBADO', 'LIBERADO', 'OBSOLETO'])->default('PENDIENTE');
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
            $table->foreign('creado_por')->references('id')->on('usuarios')->onDelete('set null');
        });

        // Añadir FK de tareas_proyecto a elementos_configuracion
        Schema::table('tareas_proyecto', function (Blueprint $table) {
            $table->foreign('id_ec')->references('id')->on('elementos_configuracion');
        });

        Schema::create('versiones_ec', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('ec_id', 36);
            $table->string('version', 50);
            $table->text('registro_cambios')->nullable();
            $table->char('commit_id', 36)->nullable();
            $table->json('metadatos')->nullable();
            $table->enum('estado', ['PENDIENTE','BORRADOR','REVISION','APROBADO','LIBERADO','DEPRECADO'])->default('PENDIENTE');
            $table->char('creado_por', 36)->nullable();
            $table->char('aprobado_por', 36)->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('aprobado_en')->nullable();

            $table->foreign('ec_id')->references('id')->on('elementos_configuracion')->onDelete('cascade');
            $table->foreign('creado_por')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('aprobado_por')->references('id')->on('usuarios')->onDelete('set null');
        });

        // Añadir FK de version_actual_id en elementos_configuracion
        Schema::table('elementos_configuracion', function (Blueprint $table) {
            $table->foreign('version_actual_id')->references('id')->on('versiones_ec')->onDelete('set null');
        });

        Schema::create('relaciones_ec', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('desde_ec', 36);
            $table->char('hacia_ec', 36);
            $table->enum('tipo_relacion', ['DEPENDE_DE', 'DERIVADO_DE', 'REFERENCIA', 'REQUERIDO_POR']);
            $table->text('nota')->nullable();
            $table->foreign('desde_ec')->references('id')->on('elementos_configuracion')->onDelete('cascade');
            $table->foreign('hacia_ec')->references('id')->on('elementos_configuracion')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relaciones_ec');

        Schema::table('elementos_configuracion', function (Blueprint $table) {
            $table->dropForeign(['version_actual_id']);
        });

        Schema::dropIfExists('versiones_ec');

        Schema::table('tareas_proyecto', function (Blueprint $table) {
            $table->dropForeign(['id_ec']);
        });

        Schema::dropIfExists('elementos_configuracion');
    }
};
