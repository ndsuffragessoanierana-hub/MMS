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
// FICHIER : app/Models/TRubrique.php
// =============================================================================
class TRubrique extends Model
{
    protected $table        = 't_rubrique';      // ← singulier
    protected $primaryKey   = 'rubrique_id';
    public    $incrementing = false;
    protected $keyType      = 'string';
    protected $fillable     = ['rubrique_id', 'rubrique_libelle', 'chap_code', 'date_saisie', 'user_id'];
    protected $casts        = ['date_saisie' => 'date'];

    public $timestamps = false;
    
    public function scopeRecettes(Builder $q): Builder {
        return $q->whereHas('chapitre', fn($c) => $c->where('chap_code', 'like', 'A%'));
    }
    public function scopeDepenses(Builder $q): Builder {
        return $q->whereHas('chapitre', fn($c) => $c->where('chap_code', 'like', 'B%'));
    }

    public function ligneBudgets(): HasMany
    {
        return $this->hasMany(LigneBudget::class, 'rub_rubrique_id', 'rubrique_id');
    }

    public function ligneBudgetMensuels(): HasMany
    {
        return $this->hasMany(LigneBudgetMensuel::class, 'rub_rubrique_id', 'rubrique_id');
    }

    public function chapitre(): BelongsTo      { return $this->belongsTo(Chapitre::class, 'chap_code', 'chap_code'); }
    public function createur(): BelongsTo      { return $this->belongsTo(User::class, 'user_id'); }
    public function detailJournals(): HasMany  { return $this->hasMany(TDetailJournal::class, 'rub_rubrique_id', 'rubrique_id'); }
    public function detailRecaps(): HasMany    { return $this->hasMany(TDetailRecap::class, 'rub_rubrique_id', 'rubrique_id'); }
    
}

