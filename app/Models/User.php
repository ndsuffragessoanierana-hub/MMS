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
// FICHIER : app/Models/User.php
// =============================================================================
class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime'];

    // Rôles disponibles (miroir des ACL APEX)
    const ROLES = ['lecture', 'modif', 'ajout', 'suppr', 'admin'];

    public function peutLire():    bool { return true; }
    public function peutModifier(): bool { return in_array($this->role, ['modif','ajout','suppr','admin']); }
    public function peutAjouter(): bool { return in_array($this->role, ['ajout','suppr','admin']); }
    public function peutSupprimer(): bool { return in_array($this->role, ['suppr','admin']); }
    public function estAdmin():    bool { return $this->role === 'admin'; }

    // Relations
    public function journals(): HasMany    { return $this->hasMany(TJournal::class, 'user_id'); }
    public function rubriques(): HasMany   { return $this->hasMany(TRubrique::class, 'user_id'); }
}
