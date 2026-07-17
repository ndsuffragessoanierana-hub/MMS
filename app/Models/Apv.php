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
// FICHIER : app/Models/Apv.php
// =============================================================================
class Apv extends Model
{
    protected $table      = 'apv';                // ← singulier
    protected $primaryKey = 'idapv';
    public $incrementing = false; // Si l'ID n'est pas un nombre auto-incrémenté (ex: si c'est 'F01')
    protected $keyType = 'string'; // Si l'ID est du texte
    protected $fillable   = ['idfaritra', 'idapv','libelle_apv'];

    public $timestamps = false;

    public function faritra(): BelongsTo { return $this->belongsTo(Faritra::class, 'idfaritra', 'idfaritra'); }
    public function fideles(): HasMany   { return $this->hasMany(Fidele::class, 'idapv', 'idapv'); }
}