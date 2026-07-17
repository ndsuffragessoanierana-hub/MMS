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
// FICHIER : app/Models/FidelefikAmb.php  (table pivot enrichie)
// =============================================================================
class FidelefikAmb extends Model
{
    protected $table    = 'fidele_fikamb';        // ← singulier
    protected $fillable = [
        'idfikambanana', 'matricule', 'date_adhesion', 'code',
    ];
    protected $casts = ['date_adhesion' => 'date'];

    public function fidele(): BelongsTo      { return $this->belongsTo(Fidele::class, 'matricule', 'matricule'); }
    public function fikambanana(): BelongsTo { return $this->belongsTo(Fikambanana::class, 'idfikambanana', 'idfikambanana'); }
    public function role(): BelongsTo        { return $this->belongsTo(MembreRole::class, 'code', 'code'); }
}
