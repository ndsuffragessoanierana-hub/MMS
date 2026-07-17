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
// FICHIER : app/Models/Exercice.php
// =============================================================================
class Exercice extends Model
{
    protected $table      = 'exercice';          // ← singulier
    protected $primaryKey = 'id_exercice';
    public    $incrementing = false;   // si id_exercice est string/manuel
    protected $keyType    = 'string';  // adapter si c'est un integer auto
    protected $fillable   = ['id_exercice', 'date_debut', 'date_fin', 'remarque', 'actif'];
    protected $casts      = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    public $timestamps = false;

    public function scopeActif(Builder $q): Builder { return $q->where('actif', 'O'); } 

    public function ligneBudgets(): HasMany        { return $this->hasMany(LigneBudget::class, 'id_exercice', 'id_exercice'); }
    public function ligneBudgetMensuels(): HasMany { return $this->hasMany(LigneBudgetMensuel::class, 'id_exercice', 'id_exercice'); }

    // Accessor pour afficher dans la liste déroulante
    public function getLabelAttribute(): string
    {
        return $this->date_debut?->format('d/m/Y') . ' → ' . $this->date_fin?->format('d/m/Y')
               . ($this->actif == 'O' ? ' (actif)' : '');
    }

}
