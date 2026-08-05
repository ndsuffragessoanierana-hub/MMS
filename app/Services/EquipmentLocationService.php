<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class EquipmentLocationService
{
    /**
     * Déplace un équipement vers un emplacement.
     * Retire automatiquement toute affectation précédente (règle : un seul emplacement à la fois).
     */
    public function moveTo(int $fitIdfitaovana, string $idplace): void
    {
        DB::transaction(function () use ($fitIdfitaovana, $idplace) {
            DB::table('empla_fitaovana')
                ->where('fit_idfitaovana', $fitIdfitaovana)
                ->delete();

            DB::table('empla_fitaovana')->insert([
                'emp_idplace'     => $idplace,
                'fit_idfitaovana' => $fitIdfitaovana,
            ]);
        });
    }

    /**
     * Retire un équipement d'un emplacement précis (sans le réaffecter ailleurs).
     */
    public function remove(int $fitIdfitaovana, string $idplace): void
    {
        DB::table('empla_fitaovana')
            ->where('fit_idfitaovana', $fitIdfitaovana)
            ->where('emp_idplace', $idplace)
            ->delete();
    }

    /**
     * Retourne l'idplace actuel d'un équipement, ou null s'il n'est nulle part.
     */
    public function currentLocationOf(int $fitIdfitaovana): ?string
    {
        return DB::table('empla_fitaovana')
            ->where('fit_idfitaovana', $fitIdfitaovana)
            ->value('emp_idplace');
    }
}
