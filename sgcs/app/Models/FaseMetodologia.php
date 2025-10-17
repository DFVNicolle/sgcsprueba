<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaseMetodologia extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'fases_metodologia';

    /**
     * La clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_fase';

    /**
     * Indica si el modelo tiene timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id_metodologia',
        'nombre_fase',
        'orden',
        'descripcion',
    ];

    /**
     * Relación: Una fase pertenece a una metodología.
     */
    public function metodologia(): BelongsTo
    {
        return $this->belongsTo(Metodologia::class, 'id_metodologia', 'id_metodologia');
    }
}
