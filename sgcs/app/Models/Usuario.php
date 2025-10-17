<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UsuarioFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'usuarios';

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
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'correo',
        'nombre_completo',
        'contrasena_hash',
        'activo',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'contrasena_hash',
        'remember_token',
    ];

    /**
     * Obtener los atributos que deben ser convertidos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'correo_verificado_en' => 'datetime',
            'contrasena_hash' => 'hashed',
            'activo' => 'boolean',
            'creado_en' => 'datetime',
            'actualizado_en' => 'datetime',
        ];
    }

    /**
     * Obtener el identificador único para el usuario.
     */
    public function getAuthIdentifierName()
    {
        return 'correo'; // Usar correo en lugar de email para autenticación
    }

    /**
     * Obtener la contraseña para autenticación.
     */
    public function getAuthPassword()
    {
        return $this->contrasena_hash;
    }

    /**
     * Obtener el identificador de email único para el usuario.
     */
    public function getEmailForVerification()
    {
        return $this->correo;
    }

    /**
     * Determinar si el usuario ha verificado su dirección de email.
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->correo_verificado_en);
    }

    /**
     * Marcar la dirección de email como verificada.
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'correo_verificado_en' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Enviar la notificación de verificación de email.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmail);
    }

    /**
     * Enviar la notificación de restablecimiento de contraseña.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPassword($token));
    }

    // Atributos accesores para compatibilidad con Breeze
    public function getEmailAttribute()
    {
        return $this->correo;
    }

    public function getNameAttribute()
    {
        return $this->nombre_completo;
    }

    public function getPasswordAttribute()
    {
        return $this->contrasena_hash;
    }

    public function getEmailVerifiedAtAttribute()
    {
        return $this->correo_verificado_en;
    }

    // Mutadores para compatibilidad con Breeze
    public function setEmailAttribute($value)
    {
        $this->attributes['correo'] = $value;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nombre_completo'] = $value;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['contrasena_hash'] = $value;
    }

    // Relaciones
    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'usuarios_roles', 'usuario_id', 'proyecto_id')
            ->withPivot('rol_id')
            ->withTimestamps();
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuarios_roles', 'usuario_id', 'rol_id')
            ->withPivot('proyecto_id')
            ->withTimestamps();
    }
}
