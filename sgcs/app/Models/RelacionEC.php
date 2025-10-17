<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RelacionEC extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $table = 'relaciones_ec';

    protected $fillable = [
        'id',
        'desde_ec',
        'hacia_ec',
        'tipo_relacion',
        'nota',
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
     * Elemento de configuración origen (desde)
     */
    public function elementoDesde(): BelongsTo
    {
        return $this->belongsTo(ElementoConfiguracion::class, 'desde_ec');
    }

    /**
     * Elemento de configuración destino (hacia)
     */
    public function elementoHacia(): BelongsTo
    {
        return $this->belongsTo(ElementoConfiguracion::class, 'hacia_ec');
    }

    /**
     * Obtener el nombre legible del tipo de relación
     */
    public function getTipoRelacionNombreAttribute(): string
    {
        return match($this->tipo_relacion) {
            'DEPENDE_DE' => 'Depende de',
            'DERIVADO_DE' => 'Derivado de',
            'REFERENCIA' => 'Referencia a',
            'REQUERIDO_POR' => 'Requerido por',
            default => $this->tipo_relacion,
        };
    }

    /**
     * Obtener badge CSS según tipo de relación
     */
    public function getTipoBadgeAttribute(): string
    {
        return match($this->tipo_relacion) {
            'DEPENDE_DE' => 'badge-error',
            'DERIVADO_DE' => 'badge-warning',
            'REFERENCIA' => 'badge-info',
            'REQUERIDO_POR' => 'badge-success',
            default => 'badge-ghost',
        };
    }
}
