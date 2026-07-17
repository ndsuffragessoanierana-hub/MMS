<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\{LigneBudget, LigneBudgetMensuel, Exercice, TRubrique, Mois};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    // ─────────────────────────────────────────────
    // BUDGET ANNUEL
    // ─────────────────────────────────────────────

    public function annuel(Request $request)
    {
        $exercices  = Exercice::orderByDesc('id_exercice')->get();
        $exerciceId = $request->get('id_exercice', $exercices->first()?->id_exercice);
        $exercice   = Exercice::find($exerciceId);

        $rubriques = TRubrique::with([
            'chapitre',
            'ligneBudgets' => fn($q) => $q->where('id_exercice', $exerciceId)
        ])->orderBy('chap_code')->orderBy('rubrique_id')->get();

        return view('finances.budget.annuel', compact('exercices', 'exercice', 'rubriques'));
    }

    public function storeAnnuel(Request $request)
    {
        $request->validate([
            'id_exercice' => 'required|exists:exercice,id_exercice',
        ]);

        if ($request->has('lignes')) {
            foreach ($request->lignes as $ligne) {
                if (empty($ligne['rub'])) continue;

                $montant = is_numeric($ligne['montant'] ?? null) ? (float)$ligne['montant'] : 0;

                // updateOrInsert évite le problème de clé primaire Eloquent
                DB::table('ligne_budget')->updateOrInsert(
                    [
                        'id_exercice'     => $request->id_exercice,
                        'rub_rubrique_id' => $ligne['rub'],
                    ],
                    [
                        'lg_bdg_montant' => $montant,
                        'lg_bdg_numero'  => 0,
                    ]
                );
            }
        }

        return back()->with('success', 'Budget annuel enregistré.');
    }

    // ─────────────────────────────────────────────
    // BUDGET MENSUEL
    // ─────────────────────────────────────────────

    public function mensuel(Request $request)
    {
        $exercices  = Exercice::orderByDesc('id_exercice')->get();
        $exerciceId = $request->get('id_exercice', $exercices->first()?->id_exercice);
        $exercice   = Exercice::find($exerciceId);
        $moisListe  = Mois::all();

        $rubriques = TRubrique::with([
            'chapitre',
            'ligneBudgetMensuels' => fn($q) => $q->where('id_exercice', $exerciceId)
        ])->orderBy('chap_code')->orderBy('rubrique_id')->get();

        return view('finances.budget.mensuel', compact('exercices', 'exercice', 'rubriques', 'moisListe'));
    }

    public function storeMensuel(Request $request)
    {
        $request->validate([
            'id_exercice' => 'required|exists:exercice,id_exercice',
        ]);

        if ($request->has('lignes')) {
            foreach ($request->lignes as $rubId => $moisMontants) {
                if (!is_array($moisMontants)) continue;

                foreach ($moisMontants as $mois => $montant) {
                    if (!is_numeric($mois)) continue;
                    $montant = is_numeric($montant) && $montant > 0 ? (float)$montant : 0;

                    // updateOrInsert évite le problème de clé primaire composite
                    DB::table('ligne_budget_mensuel')->updateOrInsert(
                        [
                            'id_exercice'     => $request->id_exercice,
                            'rub_rubrique_id' => $rubId,
                            'mois'            => (int) $mois,
                        ],
                        [
                            'lg_bdg_montant' => $montant,
                            'lg_bdg_numero'  => 0,
                        ]
                    );
                }
            }
        }

        return back()->with('success', 'Budget mensuel enregistré.');
    }
}
