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
// FICHIER : app/Models/Fidele.php
// =============================================================================
class Fidele extends Model
{
    protected $table        = 'fidele';           // ← singulier
    protected $primaryKey   = 'matricule';
    public    $incrementing = false;
    protected $keyType      = 'string';

    protected $fillable = [
        'matricule', 'nom', 'prenom', 'nom_bapteme', 'sexe',
        'date_naissance', 'lieu_naissance',
        'date_bapteme', 'lieu_bapteme', 'nom_pretre', 'tuteur',
        'date_confesse', 'date_communion', 'date_confirmation',
        'date_mariage', 'date_ordination', 'date_deces',
        'nom_pere', 'nom_mere', 'numero_famille',
        'idfaritra', 'idapv',
        'date_arrivee', 'date_integration', 'adresse',
        'quitte', 'statut', 'numero_registre', 'profession', 'observation',
    ];

    protected $casts = [
        'date_naissance'    => 'date',
        'date_bapteme'      => 'date',
        'date_confesse'     => 'date',
        'date_communion'    => 'date',
        'date_confirmation' => 'date',
        'date_mariage'      => 'date',
        'date_ordination'   => 'date',
        'date_deces'        => 'date',
        'date_arrivee'      => 'date',
        'date_integration'  => 'date',
    ];

    public function scopeActifs(Builder $q): Builder    { return $q->where('quitte', 'N'); }
    public function scopePartis(Builder $q): Builder    { return $q->where('quitte', 'O'); }
    public function scopeDecedes(Builder $q): Builder   { return $q->whereNotNull('date_deces'); }
    public function scopeMasculin(Builder $q): Builder  { return $q->where('sexe', 'L'); }
    public function scopeFeminin(Builder $q): Builder   { return $q->where('sexe', 'V'); }
    public function scopeFaritra(Builder $q, int $id): Builder { return $q->where('idfaritra', $id); }
    public function scopeApv(Builder $q, int $id): Builder     { return $q->where('idapv', $id); }
    public function scopeRecherche(Builder $q, string $terme): Builder {
        return $q->where(fn($s) => $s
            ->where('nom',    'ilike', "%{$terme}%")
            ->orWhere('prenom', 'ilike', "%{$terme}%")
            ->orWhere('matricule', 'ilike', "%{$terme}%")
        );
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->nom . ' ' . $this->prenom);
    }
    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance
            ? $this->date_naissance->age
            : null;
    }
    public function getEstDecedeAttribute(): bool
    {
        return !is_null($this->date_deces);
    }
    public function getEstMariesAttribute(): bool
    {
        return !is_null($this->date_mariage);
    }
    public function getSacrementsAttribute(): array
    {
        $s = [];
        if ($this->date_bapteme)      $s[] = 'Baptême';
        if ($this->date_confesse)     $s[] = 'Confession';
        if ($this->date_communion)    $s[] = 'Communion';
        if ($this->date_confirmation) $s[] = 'Confirmation';
        if ($this->date_mariage)      $s[] = 'Mariage';
        if ($this->date_ordination)   $s[] = 'Ordination';
        return $s;
    }

    public function faritra(): BelongsTo         { return $this->belongsTo(Faritra::class, 'idfaritra', 'idfaritra'); }
    public function apv(): BelongsTo             { return $this->belongsTo(Apv::class, 'idapv', 'idapv'); }
    public function fikambananas(): BelongsToMany {
        return $this->belongsToMany(
            Fikambanana::class,
            'fidele_fikamb',           // ← singulier (table pivot)
            'matricule',
            'idfikambanana'
        )->withPivot(['date_adhesion', 'code']);
    }
}

