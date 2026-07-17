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
// FICHIER : app/Models/TDetailVersement.php
// =============================================================================
class TDetailVersement extends Model
{
    protected $table    = 't_detail_versement';  // ← singulier
    protected $fillable = ['j_detail_numero', 'montant', 'date_versement', 'observation'];
    protected $casts    = ['date_versement' => 'date', 'montant' => 'decimal:2'];

    public function detailJournal(): BelongsTo {
        return $this->belongsTo(TDetailJournal::class, 'j_detail_numero', 'j_detail_numero');
    }
}