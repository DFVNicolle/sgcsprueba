<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Relación: Un rol tiene muchos usuarios.
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_roles', 'rol_id', 'usuario_id')
            ->withPivot('proyecto_id')
            ->withTimestamps();
    }
}
