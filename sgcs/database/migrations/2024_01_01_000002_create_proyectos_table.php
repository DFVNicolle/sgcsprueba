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
        // Tabla de metodologías
        Schema::create('metodologias', function (Blueprint $table) {
            $table->id('id_metodologia');
            $table->string('nombre', 100);
            $table->string('tipo', 50)->nullable();
            $table->string('descripcion', 255)->nullable();
        });

        // Tabla de fases de metodología
        Schema::create('fases_metodologia', function (Blueprint $table) {
            $table->id('id_fase');
            $table->unsignedBigInteger('id_metodologia');
            $table->string('nombre_fase', 100);
            $table->integer('orden')->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->foreign('id_metodologia')->references('id_metodologia')->on('metodologias');
        });

        // Tabla de proyectos
        Schema::create('proyectos', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('id_metodologia');
            $table->string('link_repositorio', 255)->nullable();
            $table->char('creado_por', 36);
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('id_metodologia')->references('id_metodologia')->on('metodologias');
            $table->foreign('creado_por')->references('id')->on('usuarios');
        });

        // Añadir FK de usuarios_roles a proyectos
        Schema::table('usuarios_roles', function (Blueprint $table) {
            $table->foreign('proyecto_id')->references('id')->on('proyectos');
        });

        // Tabla de equipos
        Schema::create('equipos', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('proyecto_id', 36);
            $table->string('nombre', 255);
            $table->char('lider_id', 36);
            $table->foreign('proyecto_id')->references('id')->on('proyectos');
            $table->foreign('lider_id')->references('id')->on('usuarios');
        });

        // Tabla de miembros de equipo
        Schema::create('miembros_equipo', function (Blueprint $table) {
            $table->char('equipo_id', 36);
            $table->char('usuario_id', 36);
            $table->unsignedBigInteger('rol_id');
            $table->primary(['equipo_id', 'usuario_id', 'rol_id']);
            $table->foreign('equipo_id')->references('id')->on('equipos');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('rol_id')->references('id')->on('roles');
        });

        // Tabla de tareas de proyecto
        Schema::create('tareas_proyecto', function (Blueprint $table) {
            $table->id('id_tarea');
            $table->char('id_proyecto', 36);
            $table->unsignedBigInteger('id_fase');
            $table->char('id_ec', 36)->nullable();
            $table->char('responsable', 36)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('estado', 50)->nullable();
            $table->foreign('id_proyecto')->references('id')->on('proyectos');
            $table->foreign('id_fase')->references('id_fase')->on('fases_metodologia');
            // FK a elementos_configuracion se añadirá después
            $table->foreign('responsable')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas_proyecto');
        Schema::dropIfExists('miembros_equipo');
        Schema::dropIfExists('equipos');

        Schema::table('usuarios_roles', function (Blueprint $table) {
            $table->dropForeign(['proyecto_id']);
        });

        Schema::dropIfExists('proyectos');
        Schema::dropIfExists('fases_metodologia');
        Schema::dropIfExists('metodologias');
    }
};
