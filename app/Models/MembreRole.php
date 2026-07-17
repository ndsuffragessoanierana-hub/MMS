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
// FICHIER : app/Models/MembreRole.php
// =============================================================================
class MembreRole extends Model
{
    protected $table        = 'membre_role';      // ← singulier
    protected $primaryKey   = 'code';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public    $timestamps   = false;               // votre migration n'a pas de timestamps()
    protected $fillable     = ['code', 'libelle'];

    public function fidelefikambs(): HasMany { return $this->hasMany(FidelefikAmb::class, 'code', 'code'); }
}