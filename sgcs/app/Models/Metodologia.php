<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Metodologia extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'metodologias';

    /**
     * La clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_metodologia';

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
        'nombre',
        'tipo',
        'descripcion',
    ];

    /**
     * Relación: Una metodología tiene muchos proyectos.
     */
    public function proyectos(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'id_metodologia', 'id_metodologia');
    }

    /**
     * Relación: Una metodología tiene muchas fases.
     */
    public function fases(): HasMany
    {
        return $this->hasMany(FaseMetodologia::class, 'id_metodologia', 'id_metodologia');
    }
}
