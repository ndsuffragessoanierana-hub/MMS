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
// FICHIER : app/Models/Mois.php
// =============================================================================
class Mois extends Model
{
    public $timestamps  = false;
    protected $table    = 'mois';
    protected $primaryKey = 'numero';
    public    $incrementing = false;
    protected $keyType  = 'int';

    protected $fillable = ['numero', 'libelle_mois_fr', 'libelle_mois_en'];

    // Relations
    public function journals(): HasMany          { return $this->hasMany(TJournal::class, 'journal_mois', 'numero'); }
    public function detailRecaps(): HasMany      { return $this->hasMany(TDetailRecap::class, 'rec_rec_mois', 'numero'); }
    public function ligneBudgetMensuels(): HasMany { return $this->hasMany(LigneBudgetMensuel::class, 'mois', 'numero'); }

    // Accessor : libellé selon la locale
    public function getLibelleAttribute(): string
    {
        return app()->getLocale() === 'en'
            ? $this->libelle_mois_en
            : $this->libelle_mois_fr;
    }
}