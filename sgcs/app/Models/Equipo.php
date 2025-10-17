<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipo extends Model
{
    use HasFactory, HasUuids;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'equipos';

    /**
     * Indica si el ID del modelo es auto-incrementable.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * El tipo de dato del ID auto-incrementable.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indica que no usa timestamps automáticos de Laravel.
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
        'id',
        'proyecto_id',
        'nombre',
        'lider_id',
    ];

    /**
     * Relación: Un equipo pertenece a un proyecto.
     */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    /**
     * Relación: Un equipo tiene un líder (usuario).
     */
    public function lider(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'lider_id');
    }

    /**
     * Relación: Un equipo tiene muchos miembros.
     */
    public function miembros(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'miembros_equipo', 'equipo_id', 'usuario_id')
            ->withPivot('rol_id');
    }
}
