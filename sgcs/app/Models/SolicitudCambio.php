<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SolicitudCambio extends Model
{
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $table = 'solicitudes_cambio';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'proyecto_id',
        'titulo',
        'descripcion',
        'solicitante_id',
        'prioridad',
        'estado',
        'resumen_impacto',
        'liberacion_objetivo_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }

    // Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'solicitante_id');
    }

    public function liberacion()
    {
        return $this->belongsTo(Liberacion::class, 'liberacion_objetivo_id');
    }

    public function items()
    {
        return $this->hasMany(ItemCambio::class, 'solicitud_cambio_id');
    }

    public function votos()
    {
        return $this->hasMany(VotoCCB::class, 'solicitud_cambio_id');
    }
}
