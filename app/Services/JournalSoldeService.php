<?php

namespace App\Services;

use App\Models\TJournal;

class JournalSoldeService
{
    /**
     * Calcule l'impact (delta signé) d'une écriture sur les soldes du journal,
     * en reproduisant fidèlement la logique du trigger Oracle INSERT_DETAIL_JOURNAL.
     *
     * @return array{bni: float, bfv: float, caisse: float}
     */
    public function calculerImpact(string $modePaie, string $rubriqueId, float $montant): array
    {
        $bni = 0.0;
        $bfv = 0.0;
        $caisse = 0.0;

        if ($modePaie === 'ESP') {
            if (str_starts_with($rubriqueId, 'A')) {
                $caisse += $montant;
            } elseif (str_starts_with($rubriqueId, 'B')) {
                $caisse -= $montant;
            }

            return compact('bni', 'bfv', 'caisse');
        }

        if (str_starts_with($rubriqueId, 'A')) {
            if ($modePaie === 'BNI') {
                $bni += $montant;
            } elseif ($modePaie === 'BFV') {
                $bfv += $montant;
            }
        } elseif (str_starts_with($rubriqueId, 'B')) {
            if ($modePaie === 'BNI') {
                $bni -= $montant;
            } elseif ($modePaie === 'BFV') {
                $bfv -= $montant;
            }
        } elseif ($rubriqueId === '502') {
            // Virement caisse -> banque
            $caisse -= $montant;
            if ($modePaie === 'BNI') {
                $bni += $montant;
            } elseif ($modePaie === 'BFV') {
                $bfv += $montant;
            }
        } elseif ($rubriqueId === '79999') {
            // Virement banque -> caisse
            if ($modePaie === 'BNI') {
                $bni -= $montant;
            } elseif ($modePaie === 'BFV') {
                $bfv -= $montant;
            }
            $caisse += $montant;
        } elseif ($rubriqueId === '501') {
            // Sortie banque simple
            if ($modePaie === 'BNI') {
                $bni -= $montant;
            } elseif ($modePaie === 'BFV') {
                $bfv -= $montant;
            }
        } elseif ($rubriqueId === '503') {
            // Entrée banque simple
            if ($modePaie === 'BNI') {
                $bni += $montant;
            } elseif ($modePaie === 'BFV') {
                $bfv += $montant;
            }
        }

        return compact('bni', 'bfv', 'caisse');
    }

    /**
     * Applique un delta aux soldes d'un journal, avec verrou pessimiste
     * (évite les pertes de mise à jour en cas d'écritures concurrentes).
     */
    public function appliquerDelta(int $journalId, array $delta): void
    {
        /** @var TJournal $journal */
        $journal = TJournal::where('journal_id', $journalId)->lockForUpdate()->firstOrFail();

        $journal->journal_solde_bni    += $delta['bni'];
        $journal->journal_solde_bfv    += $delta['bfv'];
        $journal->journal_solde_caisse += $delta['caisse'];
        $journal->save();
    }

    /** Enregistre l'effet d'une nouvelle écriture sur le journal. */
    public function enregistrerEcriture(int $journalId, string $modePaie, string $rubriqueId, float $montant): void
    {
        $this->appliquerDelta($journalId, $this->calculerImpact($modePaie, $rubriqueId, $montant));
    }

    /** Annule l'effet d'une écriture existante (avant modification ou suppression). */
    public function annulerEcriture(int $journalId, string $modePaie, string $rubriqueId, float $montant): void
    {
        $delta = $this->calculerImpact($modePaie, $rubriqueId, $montant);
        $this->appliquerDelta($journalId, [
            'bni'    => -$delta['bni'],
            'bfv'    => -$delta['bfv'],
            'caisse' => -$delta['caisse'],
        ]);
    }
}