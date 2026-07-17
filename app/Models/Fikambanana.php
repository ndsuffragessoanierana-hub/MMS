<?php

// =============================================================================
// ECAR Masina Maria Mpanampy — Models Eloquent Laravel
// Copiez chaque classe dans app/Models/ avec le nom de fichier indiqué
// =============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsTo, BelongsToMany};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// =============================================================================
// FICHIER : app/Models/Fikambanana.php
// =============================================================================
class Fikambanana extends Model
{
    protected $table      = 'fikambanana';        // ← singulier
    protected $primaryKey = 'idfikambanana';
    protected $fillable   = ['libelle_fikambanana', 'st_patron'];

    public function fideles(): BelongsToMany {
        return $this->belongsToMany(
            Fidele::class,
            'fidele_fikamb',           // ← singulier (table pivot)
            'idfikambanana',
            'matricule'
        )->withPivot(['date_adhesion', 'code']);
    }
    public function membres(): HasMany { return $this->hasMany(FidelefikAmb::class, 'idfikambanana', 'idfikambanana'); }

    public function getNombresMembresAttribute(): int
    {
        return $this->fideles()->count();
    }
}