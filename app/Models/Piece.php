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
// FICHIER : app/Models/Piece.php
// =============================================================================
class Piece extends Model
{
    protected $table    = 'pieces';
    protected $primaryKey = 'piece_id';
    protected $fillable = ['piece_date', 'piece_libelle', 'piece_copie'];
    protected $casts    = ['piece_date' => 'date'];

    // Accessor : URL du fichier numérisé
    public function getUrlCopieAttribute(): ?string
    {
        return $this->piece_copie ? asset('storage/' . $this->piece_copie) : null;
    }
}