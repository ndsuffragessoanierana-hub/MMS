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
// FICHIER : app/Models/Agenda.php
// =============================================================================
class Agenda extends Model
{
    protected $table      = 'agenda';
    protected $primaryKey = 'id_agenda';
    protected $fillable   = ['date_agenda', 'libelle', 'observation'];
    protected $casts      = ['date_agenda' => 'date'];

    public $timestamps = false;

    // Scopes
    public function scopeAVenir(Builder $q): Builder {
        return $q->where('date_agenda', '>=', now()->toDateString())->orderBy('date_agenda');
    }
    public function scopeMois(Builder $q, int $mois, int $annee): Builder {
        return $q->whereMonth('date_agenda', $mois)->whereYear('date_agenda', $annee);
    }
    public function scopeSemaine(Builder $q): Builder {
        return $q->whereBetween('date_agenda', [now()->startOfWeek(), now()->endOfWeek()]);
    }
}
