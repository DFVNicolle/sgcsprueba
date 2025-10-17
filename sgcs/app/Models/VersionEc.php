<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VersionEc extends Model
{
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null; // No tiene updated_at

    protected $table = 'versiones_ec';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ec_id',
        'version',
        'registro_cambios',
        'commit_id', // Cambiado de archivo_id
        'estado',
        'creado_por',
        'aprobado_por',
        'aprobado_en',
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

    /**
     * Relación con ElementoConfiguracion
     */
    public function elementoConfiguracion(): BelongsTo
    {
        return $this->belongsTo(ElementoConfiguracion::class, 'ec_id');
    }

    /**
     * Relación con Commit de GitHub
     */
    public function commit(): BelongsTo
    {
        return $this->belongsTo(CommitRepositorio::class, 'commit_id');
    }

    /**
     * Relación con Usuario (quien creó)
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    /**
     * Relación con Usuario (quien aprobó)
     */
    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aprobado_por');
    }
}
