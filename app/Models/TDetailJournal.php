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
// FICHIER : app/Models/TDetailJournal.php
// =============================================================================
class TDetailJournal extends Model
{
    protected $table      = 't_detail_journal';  // ← singulier
    protected $primaryKey = 'j_detail_numero';
    protected $fillable   = [
        'j_detail_date', 'j_detail_libelle', 'j_detail_mode_paie',
        'j_detail_montant', 'jrl_journal_id', 'rub_rubrique_id', 'cpt_no_compte',
    ];
    protected $casts = [
        'j_detail_date'    => 'date',
        'j_detail_montant' => 'decimal:2',
    ];
    public $timestamps = false;
    
    const MODES_PAIE = [
            'ESP' => 'ESPECE',
            'BFV' => 'BRED',
            'BNI' => 'BNI',
            'VIR' => 'VIREMENT',
            'MOB' => 'MOBILE',
        ];

    public function scopeParMois(Builder $q, int $mois, int $annee): Builder {
        return $q->whereHas('journal', fn($j) => $j->where('journal_mois', $mois)->where('journal_annee', $annee));
    }
    public function scopeParRubrique(Builder $q, string $rubId): Builder {
        return $q->where('rub_rubrique_id', $rubId);
    }
    public function scopeParCompte(Builder $q, string $compte): Builder {
        return $q->where('cpt_no_compte', $compte);
    }

    public function journal(): BelongsTo    { return $this->belongsTo(TJournal::class, 'jrl_journal_id', 'journal_id'); }
    public function rubrique(): BelongsTo   { return $this->belongsTo(TRubrique::class, 'rub_rubrique_id', 'rubrique_id'); }
    public function compte(): BelongsTo     { return $this->belongsTo(Compte::class, 'cpt_no_compte', 'no_compte'); }
    public function versements(): HasMany   { return $this->hasMany(TDetailVersement::class, 'j_detail_numero', 'j_detail_numero'); }

    public function getModeLibelleAttribute(): string
    {
        return self::MODES_PAIE[$this->j_detail_mode_paie] ?? $this->j_detail_mode_paie ?? '—';
    }
}
