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
// FICHIER : app/Models/Chapitre.php
// =============================================================================
class Chapitre extends Model
{
    protected $table        = 'chapitre';        // ← singulier
    protected $primaryKey   = 'chap_code';
    public    $incrementing = false;
    protected $keyType      = 'string';
    protected $fillable     = ['chap_code', 'chap_libelle', 'actuel'];

    public function scopeActuel(Builder $q): Builder   { return $q->where('actuel', 'O'); }
    public function scopeRecettes(Builder $q): Builder { return $q->where('chap_code', 'like', 'A%'); }
    public function scopeDepenses(Builder $q): Builder { return $q->where('chap_code', 'like', 'B%'); }

    public function rubriques(): HasMany { return $this->hasMany(TRubrique::class, 'chap_code', 'chap_code'); }

    public function getEstRecetteAttribute(): bool { return str_starts_with($this->chap_code, 'A'); }
    public function getEstDepenseAttribute(): bool { return str_starts_with($this->chap_code, 'B'); }
}
