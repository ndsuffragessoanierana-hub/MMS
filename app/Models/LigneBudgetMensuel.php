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
// FICHIER : app/Models/LigneBudgetMensuel.php
// =============================================================================
class LigneBudgetMensuel extends Model
{
    protected $table        = 'ligne_budget_mensuel';
    protected $primaryKey   = null;   // ← pas de PK simple
    public    $incrementing = false;
    public    $timestamps   = false;  // ← pas de created_at/updated_at non plus

    protected $fillable = [
        'id_exercice', 'lg_bdg_numero',
        'rub_rubrique_id', 'lg_bdg_montant', 'mois'
    ];
    protected $casts = ['lg_bdg_montant' => 'decimal:2'];

    public function exercice(): BelongsTo {
        return $this->belongsTo(Exercice::class, 'id_exercice', 'id_exercice');
    }
    public function rubrique(): BelongsTo {
        return $this->belongsTo(TRubrique::class, 'rub_rubrique_id', 'rubrique_id');
    }
    public function moisRef(): BelongsTo {
        return $this->belongsTo(Mois::class, 'mois', 'numero');
    }
}
