<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TareaProyecto extends Model
{
    protected $table = 'tareas_proyecto';
    protected $primaryKey = 'id_tarea';
    public $timestamps = false; // Usamos creado_en y actualizado_en personalizados

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'id_proyecto',
        'id_fase',
        'id_ec',
        'nombre',
        'descripcion',
        'responsable',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'story_points',
        'horas_estimadas',
        'prioridad',
        'sprint',
        'criterios_aceptacion',
        'notas',
    ];

    protected $casts = [
        'criterios_aceptacion' => 'array',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'story_points' => 'integer',
        'horas_estimadas' => 'decimal:2',
        'prioridad' => 'integer',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime',
    ];

    /**
     * Relación con Proyecto
     */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    /**
     * Relación con Fase de Metodología
     */
    public function fase(): BelongsTo
    {
        return $this->belongsTo(FaseMetodologia::class, 'id_fase', 'id_fase');
    }

    /**
     * Relación con Elemento de Configuración
     */
    public function elementoConfiguracion(): BelongsTo
    {
        return $this->belongsTo(ElementoConfiguracion::class, 'id_ec', 'id');
    }

    /**
     * Relación con Usuario (responsable)
     */
    public function responsableUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsable', 'id');
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para filtrar por fase
     */
    public function scopeFase($query, $idFase)
    {
        return $query->where('id_fase', $idFase);
    }

    /**
     * Scope para filtrar por sprint
     */
    public function scopeSprint($query, $sprint)
    {
        return $query->where('sprint', $sprint);
    }

    /**
     * Verifica si la tarea está en revisión
     */
    public function estaEnRevision(): bool
    {
        return in_array($this->estado, ['EN_REVISION', 'In Review', 'Review']);
    }

    /**
     * Verifica si la tarea está completada
     */
    public function estaCompletada(): bool
    {
        return in_array($this->estado, ['COMPLETADA', 'Done', 'DONE']);
    }
}
