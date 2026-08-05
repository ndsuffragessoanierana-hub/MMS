<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Emplacement extends Model
{
    protected $table = 'emplacement';

    protected $primaryKey = 'idplace';

    // idplace est un varchar défini manuellement (ex: "1", "10"), pas un auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Pas de created_at / updated_at sur cette table de référence
    public $timestamps = false;

    protected $fillable = [
        'idplace',
        'libelle_place',
        'toerana',
        'nom_court',
    ];

    /**
     * Les équipements rattachés à cet emplacement.
     * Adapte "idfitaovana" si la clé primaire de Fitaovana porte un autre nom.
     */
    public function fitaovanas(): BelongsToMany
    {
        return $this->belongsToMany(
            Fitaovana::class,
            'empla_fitaovana',
            'emp_idplace',
            'fit_idfitaovana',
            'idplace',
            'idfitaovana'
        );
    }
}
