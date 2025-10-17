<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            // Agregar columna metodologia_id como foreign key (sin after, para evitar error)
            $table->unsignedBigInteger('metodologia_id')->nullable();

            // Crear foreign key a la tabla metodologias (usar id_metodologia)
            $table->foreign('metodologia_id')
                  ->references('id_metodologia')
                  ->on('metodologias')
                  ->onDelete('set null');

            // Crear índice para optimizar consultas
            $table->index('metodologia_id');
        });

        // (No migración de datos, columna 'metodologia' no existe en la tabla proyectos)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropForeign(['metodologia_id']);
            $table->dropIndex(['metodologia_id']);
            $table->dropColumn('metodologia_id');
        });
    }
};
