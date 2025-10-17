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
        Schema::create('liberaciones', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('proyecto_id', 36); // NOT NULL
            $table->string('etiqueta', 50);
            $table->string('nombre', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_liberacion')->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->foreign('proyecto_id')->references('id')->on('proyectos');
        });

        Schema::create('items_liberacion', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('liberacion_id', 36);
            $table->char('ec_id', 36)->nullable();
            $table->char('version_ec_id', 36)->nullable();
            $table->foreign('liberacion_id')->references('id')->on('liberaciones')->onDelete('cascade');
            $table->foreign('ec_id')->references('id')->on('elementos_configuracion');
            $table->foreign('version_ec_id')->references('id')->on('versiones_ec');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_liberacion');
        Schema::dropIfExists('liberaciones');
    }
};
