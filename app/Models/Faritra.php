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
// FICHIER : app/Models/Faritra.php
// =============================================================================
class Faritra extends Model
{
    protected $table      = 'faritra';            // ← singulier
    protected $primaryKey = 'idfaritra';
    public $incrementing = false; // Si l'ID n'est pas un nombre auto-incrémenté (ex: si c'est 'F01')
    protected $keyType = 'string'; // Si l'ID est du texte
    protected $fillable   = ['libelle_faritra', 'st_patron', 'sigle', 'num_ordre_faritra'];

    public $timestamps = false;

    public function scopeOrdonne(Builder $q): Builder {
        return $q->orderBy('num_ordre_faritra')->orderBy('libelle_faritra');
    }

    public function apvs(): HasMany    { return $this->hasMany(Apv::class, 'idfaritra', 'idfaritra'); }
    public function fideles(): HasMany { return $this->hasMany(Fidele::class, 'idfaritra', 'idfaritra'); }

    public function getNombreFidelesAttribute(): int
    {
        return $this->fideles()->where('quitte', 'N')->count();
    }
}
