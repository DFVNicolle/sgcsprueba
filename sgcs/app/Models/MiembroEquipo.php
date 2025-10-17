<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MiembroEquipo extends Model
{
    protected $table = 'miembros_equipo';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'equipo_id',
        'usuario_id',
        'rol_id',
    ];

    // Clave primaria compuesta
    protected $primaryKey = ['equipo_id', 'usuario_id'];

    // Relaciones
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
}
