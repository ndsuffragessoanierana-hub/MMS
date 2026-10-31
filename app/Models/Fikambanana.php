<?php

// =============================================================================
// FICHIER : app/Models/Fikambanana.php
// =============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsToMany};

class Fikambanana extends Model
{
    protected $table      = 'fikambanana';        // ← singulier
    protected $primaryKey = 'idfikambanana';
    public $incrementing  = false;                // clé non auto-incrémentée
    protected $keyType    = 'string';             // clé texte (LMK, VVF, ...)

    protected $fillable   = ['idfikambanana', 'libelle_fikambanana', 'st_patron'];

    public function fideles(): BelongsToMany {
        return $this->belongsToMany(
            Fidele::class,
            'fidele_fikamb',           // ← singulier (table pivot)
            'idfikambanana',
            'matricule'
        )->withPivot(['date_adhesion', 'code']);
    }

    public function membres(): HasMany {
        return $this->hasMany(FidelefikAmb::class, 'idfikambanana', 'idfikambanana');
    }

    public function getNombresMembresAttribute(): int
    {
        return $this->fideles()->count();
    }
}
