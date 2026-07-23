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
// FICHIER : app/Models/Fitaovana.php
// =============================================================================
class Fitaovana extends Model
{
    protected $table      = 'fitaovana';          // ← singulier
    protected $primaryKey = 'idfitaovana';
    protected $fillable   = [
        'denomination', 'reference', 'date_acquisition', 'valeur_acquisition',
        'qte_achetee', 'fournisseur', 'no_inventaire',
        'tf_id_type_fitaovana', 'remarque', 'qr_text',
    ];
    protected $casts = [
        'date_acquisition'  => 'date',
        'valeur_acquisition'=> 'decimal:2',
    ];

    public function scopeParType(Builder $q, int $typeId): Builder {
        return $query->where('tf_id_type_fitaovana', (string) $typeId);
    }
    public function scopeRecherche(Builder $q, string $terme): Builder {
        return $q->where(fn($s) => $s
            ->where('denomination',  'ilike', "%{$terme}%")
            ->orWhere('no_inventaire','ilike', "%{$terme}%")
            ->orWhere('reference',    'ilike', "%{$terme}%")
        );
    }

    public function typeFitaovana(): BelongsTo {
        return $this->belongsTo(TypeFitaovana::class, 'tf_id_type_fitaovana', 'id_type_fitaovana');
    }

    public function getValeurTotaleAttribute(): float
    {
        return (float)$this->valeur_acquisition * $this->qte_achetee;
    }

    public function getQrDataAttribute(): array
    {
        return [
            'id'          => $this->idfitaovana,
            'no'          => $this->no_inventaire,
            'denomination'=> $this->denomination,
            'ref'         => $this->reference,
        ];
    }
}
