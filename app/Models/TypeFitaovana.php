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
// FICHIER : app/Models/TypeFitaovana.php
// =============================================================================
class TypeFitaovana extends Model
{
    protected $table      = 'type_fitaovana';     // ← singulier
    protected $primaryKey = 'id_type_fitaovana';
    public $incrementing = false; // Si l'ID n'est pas un nombre auto-incrémenté (ex: si c'est 'F01')
    protected $keyType = 'string'; // Si l'ID est du texte
    
    protected $fillable   = ['id_type_fitaovana','libelle_type_fitaovana'];

    
    public $timestamps = false;

    public function getRouteKeyName(): string
    {
        return 'id_type_fitaovana';
    }


    public function fitaovanas(): HasMany { return $this->hasMany(Fitaovana::class, 'tf_id_type_fitaovana', 'id_type_fitaovana'); }
}
