<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Proyecto extends Model
{
    use HasFactory, HasUuids;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'proyectos';

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
     * Nombres de las columnas de timestamps personalizados.
     */
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'codigo',
        'nombre',
        'descripcion',
        'id_metodologia',
        'link_repositorio',
        'creado_por',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime',
    ];

    /**
     * Relación: Un proyecto tiene muchos equipos.
     */
    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'proyecto_id');
    }

    /**
     * Relación: Un proyecto tiene muchos elementos de configuración.
     */
    public function elementosConfiguracion(): HasMany
    {
        return $this->hasMany(ElementoConfiguracion::class, 'proyecto_id');
    }

    /**
     * Relación: Un proyecto tiene muchas tareas.
     */
    public function tareas(): HasMany
    {
        return $this->hasMany(TareaProyecto::class, 'id_proyecto', 'id');
    }

    /**
     * Relación: Un proyecto pertenece a un usuario creador.
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    /**
     * Relación: Un proyecto pertenece a una metodología.
     */
    public function metodologia()
    {
        return $this->belongsTo(Metodologia::class, 'id_metodologia', 'id_metodologia');
    }

    /**
     * Relación: Un proyecto tiene muchos usuarios a través de usuarios_roles.
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_roles', 'proyecto_id', 'usuario_id')
            ->withPivot('rol_id')
            ->withTimestamps();
    }

    /**
     * Scope para obtener proyectos activos (puedes personalizar la lógica).
     */
    public function scopeActivos($query)
    {
        return $query; // Por ahora retorna todos, puedes agregar filtros
    }
}
