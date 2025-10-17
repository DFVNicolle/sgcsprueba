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
        Schema::table('tareas_proyecto', function (Blueprint $table) {
            // Información básica
            $table->string('nombre', 255)->after('id_tarea');
            $table->text('descripcion')->nullable()->after('nombre');

            // Estimaciones y prioridad
            $table->integer('story_points')->nullable()->after('descripcion');
            $table->decimal('horas_estimadas', 8, 2)->nullable()->after('story_points');
            $table->integer('prioridad')->default(0)->after('horas_estimadas');

            // Sprint y datos adicionales
            $table->string('sprint', 50)->nullable()->after('prioridad');

            // Criterios de aceptación y notas (JSON para flexibilidad)
            $table->json('criterios_aceptacion')->nullable()->after('sprint');
            $table->text('notas')->nullable()->after('criterios_aceptacion');

            // Timestamps
            $table->timestamp('creado_en')->useCurrent()->after('notas');
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate()->after('creado_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tareas_proyecto', function (Blueprint $table) {
            $table->dropColumn([
                'nombre',
                'descripcion',
                'story_points',
                'horas_estimadas',
                'prioridad',
                'sprint',
                'criterios_aceptacion',
                'notas',
                'creado_en',
                'actualizado_en'
            ]);
        });
    }
};
