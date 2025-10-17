<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommitRepositorio extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $table = 'commits_repositorio';

    protected $fillable = [
        'id',
        'url_repositorio',
        'hash_commit',
        'autor',
        'mensaje',
        'fecha_commit',
        'ec_id',
    ];

    protected $casts = [
        'fecha_commit' => 'datetime',
    ];

    /**
     * Elemento de configuración al que pertenece este commit
     */
    public function elementoConfiguracion(): BelongsTo
    {
        return $this->belongsTo(ElementoConfiguracion::class, 'ec_id');
    }

    /**
     * Versiones que referencian este commit
     */
    public function versiones(): HasMany
    {
        return $this->hasMany(VersionEC::class, 'commit_id');
    }
}
