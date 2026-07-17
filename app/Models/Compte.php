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
// FICHIER : app/Models/Compte.php
// =============================================================================
class Compte extends Model
{
    protected $table        = 'compte';          // ← singulier
    protected $primaryKey   = 'no_compte';
    public    $incrementing = false;
    protected $keyType      = 'string';
    protected $fillable     = ['no_compte', 'libelle_compte', 'type_compte', 'actif'];

    public function scopeActif(Builder $q): Builder  { return $q->where('actif', 'O'); }
    public function scopeCaisse(Builder $q): Builder { return $q->where('type_compte', 'CAISSE'); }
    public function scopeBanque(Builder $q): Builder { return $q->whereIn('type_compte', ['BNI', 'BFV']); }

    public function detailJournals(): HasMany { return $this->hasMany(TDetailJournal::class, 'cpt_no_compte', 'no_compte'); }
}
