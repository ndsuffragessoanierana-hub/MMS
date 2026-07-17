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
// FICHIER : app/Models/TDetailRecap.php
// =============================================================================
class TDetailRecap extends Model
{
    protected $table    = 't_detail_recap';      // ← singulier
    protected $fillable = ['rub_rubrique_id', 'rec_rec_mois', 'rec_rec_annee', 'detail_rkp_montant'];
    protected $casts    = ['detail_rkp_montant' => 'decimal:2'];

    public function scopePeriode(Builder $q, int $mois, int $annee): Builder {
        return $q->where('rec_rec_mois', $mois)->where('rec_rec_annee', $annee);
    }
    public function scopeAnnee(Builder $q, int $annee): Builder {
        return $q->where('rec_rec_annee', $annee);
    }

    public function rubrique(): BelongsTo { return $this->belongsTo(TRubrique::class, 'rub_rubrique_id', 'rubrique_id'); }
    public function mois(): BelongsTo     { return $this->belongsTo(Mois::class, 'rec_rec_mois', 'numero'); }
}