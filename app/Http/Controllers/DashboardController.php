<?php

namespace App\Http\Controllers;

use App\Models\{TJournal, TDetailJournal, Fidele, Faritra, Fitaovana, Agenda, Chapitre};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Finances ──
        $dernierJournal = TJournal::with('mois')->orderByDesc('journal_annee')
            ->orderByDesc('journal_mois')->first();

        $anneeEnCours = date('Y');

        // Recettes de l'année
        $recettesAnnee = TDetailJournal::join('t_journal', 't_journal.journal_id', '=', 't_detail_journal.jrl_journal_id')
            ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
            ->join('chapitre', 'chapitre.chap_code', '=', 't_rubrique.chap_code')
            ->where('t_journal.journal_annee', $anneeEnCours)
            ->where('chapitre.chap_code', 'like', 'A%')
            ->sum('j_detail_montant');

        // Dépenses de l'année
        $depensesAnnee = TDetailJournal::join('t_journal', 't_journal.journal_id', '=', 't_detail_journal.jrl_journal_id')
            ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
            ->join('chapitre', 'chapitre.chap_code', '=', 't_rubrique.chap_code')
            ->where('t_journal.journal_annee', $anneeEnCours)
            ->where('chapitre.chap_code', 'like', 'B%')
            ->sum('j_detail_montant');

        // ── Données graphique Recettes/Dépenses (12 mois) ──
        $moisRef = \App\Models\Mois::all()->keyBy('numero');
        $chartLabels = $chartRec = $chartDep = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartLabels[] = substr($moisRef[$m]->libelle_mois_fr ?? "M{$m}", 0, 3);
            $chartRec[]    = (float) TDetailJournal::join('t_journal', 't_journal.journal_id', '=', 't_detail_journal.jrl_journal_id')
                ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
                ->join('chapitre', 'chapitre.chap_code', '=', 't_rubrique.chap_code')
                ->where('t_journal.journal_annee', $anneeEnCours)
                ->where('t_journal.journal_mois', $m)
                ->where('chapitre.chap_code', 'like', 'A%')
                ->sum('j_detail_montant');
            $chartDep[]    = (float) TDetailJournal::join('t_journal', 't_journal.journal_id', '=', 't_detail_journal.jrl_journal_id')
                ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
                ->join('chapitre', 'chapitre.chap_code', '=', 't_rubrique.chap_code')
                ->where('t_journal.journal_annee', $anneeEnCours)
                ->where('t_journal.journal_mois', $m)
                ->where('chapitre.chap_code', 'like', 'B%')
                ->sum('j_detail_montant');
        }

        // ── Fidèles ──
        $statsFideles = [
            'total'   => Fidele::actifs()->count(),
            'hommes'  => Fidele::actifs()->masculin()->count(),
            'femmes'  => Fidele::actifs()->feminin()->count(),
            'partis'  => Fidele::partis()->count(),
        ];

        // Répartition par Faritra (pour graphique camembert)
        $repartFaritra = Faritra::withCount([
            'fideles as nb' => fn($q) => $q->where('quitte', 'N')
        ])->orderByDesc('nb')->get()
          ->filter(fn($f) => $f->nb > 0)
          ->values();

        // ── Inventaire ──
        $nbEquipements    = Fitaovana::count();
        $valeurInventaire = Fitaovana::sum(DB::raw('COALESCE(valeur_acquisition,0) * qte_achetee'));

        // ── Agenda : prochains événements ──
        $prochainsEvents = Agenda::aVenir()->limit(5)->get();

        return view('dashboard', compact(
            'dernierJournal', 'anneeEnCours',
            'recettesAnnee', 'depensesAnnee',
            'chartLabels', 'chartRec', 'chartDep',
            'statsFideles', 'repartFaritra',
            'nbEquipements', 'valeurInventaire',
            'prochainsEvents'
        ));
    }
}
