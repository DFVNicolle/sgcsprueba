<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ElementoConfiguracion extends Model
{
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $table = 'elementos_configuracion';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'codigo_ec',
        'titulo',
        'descripcion',
        'proyecto_id',
        'tipo',
        'padre_id',
        'version_actual_id',
        'estado',
        'metadatos',
        'creado_por',
    ];

    protected $casts = [
        'metadatos' => 'array',
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

    public function padre()
    {
        return $this->belongsTo(ElementoConfiguracion::class, 'padre_id');
    }

    public function hijos()
    {
        return $this->hasMany(ElementoConfiguracion::class, 'padre_id');
    }

    public function versionActual()
    {
        return $this->belongsTo(VersionEC::class, 'version_actual_id');
    }

    public function versiones()
    {
        return $this->hasMany(VersionEC::class, 'ec_id');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    public function relacionesDesde()
    {
        return $this->hasMany(RelacionEC::class, 'desde_ec');
    }

    public function relacionesHacia()
    {
        return $this->hasMany(RelacionEC::class, 'hacia_ec');
    }

    /**
     * Obtener el badge de color según el estado
     */
    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'BORRADOR' => 'badge-ghost',
            'EN_REVISION' => 'badge-info',
            'APROBADO' => 'badge-success',
            'LIBERADO' => 'badge-primary',
            'OBSOLETO' => 'badge-error',
            default => 'badge-ghost',
        };
    }

    /**
     * Obtener el badge de color según el tipo
     */
    public function getTipoBadgeAttribute(): string
    {
        return match($this->tipo) {
            'DOCUMENTO' => 'badge-info',
            'CODIGO' => 'badge-success',
            'SCRIPT_BD' => 'badge-warning',
            'CONFIGURACION' => 'badge-secondary',
            'OTRO' => 'badge-ghost',
            default => 'badge-ghost',
        };
    }
}
