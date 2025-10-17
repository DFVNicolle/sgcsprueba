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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('tipo_entidad', 100)->nullable();
            $table->char('entidad_id', 36)->nullable();
            $table->string('accion', 100)->nullable();
            $table->char('usuario_id', 36)->nullable();
            $table->json('detalles')->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
        });

        Schema::create('notificaciones', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('usuario_id', 36)->nullable();
            $table->string('tipo', 50)->nullable();
            $table->json('datos')->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamp('creado_en')->useCurrent();
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('auditorias');
    }
};
