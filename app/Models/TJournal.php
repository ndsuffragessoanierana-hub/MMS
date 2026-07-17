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
// FICHIER : app/Models/TJournal.php
// =============================================================================
class TJournal extends Model
{
    protected $table      = 't_journal';         // ← singulier
    protected $primaryKey = 'journal_id';
    protected $fillable   = [
        'journal_mois', 'journal_annee',
        'journal_solde_bni', 'journal_solde_bfv', 'journal_solde_caisse',
        'user_id',
    ];
    protected $casts = [
        'journal_solde_bni'    => 'decimal:2',
        'journal_solde_bfv'    => 'decimal:2',
        'journal_solde_caisse' => 'decimal:2',
    ];

    public function scopeAnnee(Builder $q, int $annee): Builder  { return $q->where('journal_annee', $annee); }
    public function scopeMois(Builder $q, int $mois): Builder    { return $q->where('journal_mois', $mois); }
    public function scopeDernier(Builder $q): Builder            { return $q->orderByDesc('journal_id')->limit(1); }

    public function getSoldeTotalAttribute(): float
    {
        return (float)$this->journal_solde_bni
             + (float)$this->journal_solde_bfv
             + (float)$this->journal_solde_caisse;
    }
    public function getPeriodeAttribute(): string
    {
        return $this->mois?->libelle_mois_fr . ' ' . $this->journal_annee;
    }

    public function mois(): BelongsTo        { return $this->belongsTo(Mois::class, 'journal_mois', 'numero'); }
    public function createur(): BelongsTo    { return $this->belongsTo(User::class, 'user_id'); }
    public function details(): HasMany       { return $this->hasMany(TDetailJournal::class, 'jrl_journal_id', 'journal_id'); }
    public function recettes(): HasMany {
        return $this->details()->whereHas('rubrique.chapitre',
            fn($q) => $q->where('chap_code', 'like', 'A%')
        );
    }
    public function depenses(): HasMany {
        return $this->details()->whereHas('rubrique.chapitre',
            fn($q) => $q->where('chap_code', 'like', 'B%')
        );
    }

    public function totalRecettes(): float
    {
        return $this->details()
            ->whereHas('rubrique.chapitre', fn($q) => $q->where('chap_code', 'like', 'A%'))
            ->sum('j_detail_montant');
    }
    public function totalDepenses(): float
    {
        return $this->details()
            ->whereHas('rubrique.chapitre', fn($q) => $q->where('chap_code', 'like', 'B%'))
            ->sum('j_detail_montant');
    }
}