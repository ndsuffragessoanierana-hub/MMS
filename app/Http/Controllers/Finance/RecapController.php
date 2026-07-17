<?php
// =============================================================================
// ECAR — Module Finances : CRUD complet
// Livre Journal · Rubriques · Chapitres · Comptes · Budget · Récapitulatif
// =============================================================================
// Fichiers à créer :
//   app/Http/Controllers/Finance/JournalController.php
//   app/Http/Controllers/Finance/RubriqueController.php
//   app/Http/Controllers/Finance/ChapitreController.php
//   app/Http/Controllers/Finance/CompteController.php
//   app/Http/Controllers/Finance/BudgetController.php
//   app/Http/Controllers/Finance/RecapController.php
//   app/Http/Requests/StoreJournalRequest.php
//   app/Http/Requests/StoreDetailJournalRequest.php
//   app/Http/Requests/StoreRubriqueRequest.php
//   routes/web.php (extrait finances)
//   resources/views/finances/journal/index.blade.php
//   resources/views/finances/journal/show.blade.php
//   resources/views/finances/journal/_form_ecriture.blade.php
//   resources/views/finances/rubriques/index.blade.php
//   resources/views/finances/recap/index.blade.php
//   resources/views/finances/dashboard.blade.php
// =============================================================================


// =============================================================================
// FICHIER : app/Http/Controllers/Finance/RecapController.php
// =============================================================================

namespace App\Http\Controllers\Finance;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Controllers\Controller;
use App\Models\{TDetailJournal, TDetailRecap, TRubrique, Chapitre, TJournal};
use Illuminate\Http\Request;

class RecapController extends Controller
{
    // public function __construct() { $this->middleware('auth'); }

    // ------------------------------------------------------------------
    // Récapitulatif mensuel par rubrique
    // ------------------------------------------------------------------
    public function parRubrique(Request $request)
    {
        $mois  = $request->get('mois',  (int)date('m'));
        $annee = $request->get('annee', (int)date('Y'));

        // Une seule requête SQL avec jointures
        $donnees = \DB::table('t_detail_journal')
            ->join('t_journal',  't_journal.journal_id',   '=', 't_detail_journal.jrl_journal_id')
            ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
            ->join('chapitre',   'chapitre.chap_code',     '=', 't_rubrique.chap_code')
            ->where('t_journal.journal_mois',  $mois)
            ->where('t_journal.journal_annee', $annee)
            ->where(function($q) {                          // ← ajouté
                $q->where('chapitre.chap_code', 'like', 'A%')
                ->orWhere('chapitre.chap_code', 'like', 'B%');
            })
            ->selectRaw('
                t_rubrique.rubrique_id,
                t_rubrique.rubrique_libelle,
                t_rubrique.chap_code,
                chapitre.chap_libelle,
                SUM(j_detail_montant) as total
            ')
            ->groupBy(
                't_rubrique.rubrique_id',
                't_rubrique.rubrique_libelle',
                't_rubrique.chap_code',
                'chapitre.chap_libelle'
            )
            ->orderBy('t_rubrique.chap_code')
            ->orderBy('t_rubrique.rubrique_id')
            ->get()
            ->keyBy('rubrique_id');

        // Toutes les rubriques avec chapitre (pour afficher même celles à 0)
        $rubriques = \DB::table('t_rubrique')
            ->join('chapitre', 'chapitre.chap_code', '=', 't_rubrique.chap_code')
            ->select('t_rubrique.*', 'chapitre.chap_libelle')
            ->where(function($q) {                          // ← ajouté
                $q->where('chapitre.chap_code', 'like', 'A%')
                ->orWhere('chapitre.chap_code', 'like', 'B%');
            })
            ->orderBy('t_rubrique.chap_code')
            ->orderBy('t_rubrique.rubrique_id')
            ->get()
            ->groupBy('chap_code');

        // Totaux directs depuis la collection
        $totalRecettes = $donnees->filter(fn($d) => str_starts_with($d->chap_code, 'A'))->sum('total');
        $totalDepenses = $donnees->filter(fn($d) => str_starts_with($d->chap_code, 'B'))->sum('total');

        $journal   = TJournal::where('journal_mois', $mois)->where('journal_annee', $annee)->first();
        $moisListe = \App\Models\Mois::all();
        $annees    = TJournal::selectRaw('DISTINCT journal_annee')
                        ->orderByDesc('journal_annee')
                        ->pluck('journal_annee');

        return view('finances.recap.par_rubrique', compact(
            'donnees', 'rubriques', 'journal', 'mois', 'annee',
            'moisListe', 'annees', 'totalRecettes', 'totalDepenses'
        ));
    }

    // ------------------------------------------------------------------
    // Récapitulatif mensuel par chapitre
    // ------------------------------------------------------------------
    public function parChapitre(Request $request)
    {
        $mois  = $request->get('mois',  (int)date('m'));
        $annee = $request->get('annee', (int)date('Y'));

        $donnees = \DB::table('t_detail_journal')
            ->join('t_journal',  't_journal.journal_id',   '=', 't_detail_journal.jrl_journal_id')
            ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
            ->join('chapitre',   'chapitre.chap_code',     '=', 't_rubrique.chap_code')
            ->where('t_journal.journal_mois',  $mois)
            ->where('t_journal.journal_annee', $annee)
            ->where(function($q) {                          // ← ajouté
                $q->where('chapitre.chap_code', 'like', 'A%')
                ->orWhere('chapitre.chap_code', 'like', 'B%');
            })
            ->selectRaw('
                chapitre.chap_code,
                chapitre.chap_libelle,
                SUM(t_detail_journal.j_detail_montant) as total
            ')
            ->groupBy('chapitre.chap_code', 'chapitre.chap_libelle')
            ->orderBy('chapitre.chap_code')
            ->get();

        // Totaux directs depuis la collection
        $totalRecettes = $donnees->filter(fn($d) => str_starts_with($d->chap_code, 'A'))->sum('total');
        $totalDepenses = $donnees->filter(fn($d) => str_starts_with($d->chap_code, 'B'))->sum('total');

        $moisListe = \App\Models\Mois::all();
        $annees    = \App\Models\TJournal::selectRaw('DISTINCT journal_annee')
                        ->orderByDesc('journal_annee')
                        ->pluck('journal_annee');
        $journal   = \App\Models\TJournal::where('journal_mois', $mois)
                        ->where('journal_annee', $annee)
                        ->first();

        return view('finances.recap.par_chapitre', compact(
            'donnees', 'mois', 'annee', 'moisListe', 'annees', 'journal'
        ));
    }

    // ------------------------------------------------------------------
    // Évolution du solde (graphique) — données JSON pour Chart.js
    // ------------------------------------------------------------------
    public function evolutionSolde(Request $request)
    {
        $annee = $request->get('annee', (int)date('Y'));

        // Recettes par mois
        $recettes = TDetailJournal::join('t_journal', 't_journal.journal_id', '=', 't_detail_journal.jrl_journal_id')
            ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
            ->join('chapitre',   'chapitre.chap_code',    '=', 't_rubrique.chap_code')
            ->where('t_journal.journal_annee', $annee)
            ->where('chapitre.chap_code', 'like', 'A%')
            ->selectRaw('t_journal.journal_mois as mois, SUM(j_detail_montant) as total')
            ->groupBy('t_journal.journal_mois')
            ->pluck('total', 'mois');

        // Dépenses par mois
        $depenses = TDetailJournal::join('t_journal', 't_journal.journal_id', '=', 't_detail_journal.jrl_journal_id')
            ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
            ->join('chapitre',   'chapitre.chap_code',    '=', 't_rubrique.chap_code')
            ->where('t_journal.journal_annee', $annee)
            ->where('chapitre.chap_code', 'like', 'B%')
            ->selectRaw('t_journal.journal_mois as mois, SUM(j_detail_montant) as total')
            ->groupBy('t_journal.journal_mois')
            ->pluck('total', 'mois');

        // Construire les 12 mois
        $labels  = [];
        $dataRec = [];
        $dataDep = [];
        $dataSol = [];
        $solde   = 0;
        $moisRef = \App\Models\Mois::all()->keyBy('numero');

        for ($m = 1; $m <= 12; $m++) {
            $r = (float)($recettes[$m] ?? 0);
            $d = (float)($depenses[$m] ?? 0);
            $solde += ($r - $d);
            $labels[]  = $moisRef[$m]->libelle_mois_fr ?? "Mois {$m}";
            $dataRec[] = round($r, 2);
            $dataDep[] = round($d, 2);
            $dataSol[] = round($solde, 2);
        }

        $annees = TJournal::selectRaw('DISTINCT journal_annee')->orderByDesc('journal_annee')->pluck('journal_annee');

        return view('finances.recap.evolution', compact(
            'labels', 'dataRec', 'dataDep', 'dataSol', 'annee', 'annees'
        ));
    }

    // ------------------------------------------------------------------
    // Journal par compte
    // ------------------------------------------------------------------
    public function parCompte(Request $request)
    {
        $compteId  = $request->get('compte');
        $dateDebut = $request->get('date_debut', now()->startOfYear()->format('Y-m-d'));
        $dateFin   = $request->get('date_fin',   now()->format('Y-m-d'));

        $baseQuery = \DB::table('t_detail_journal')
            ->join('t_journal',  't_journal.journal_id',   '=', 't_detail_journal.jrl_journal_id')
            ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
            ->join('chapitre',   'chapitre.chap_code',     '=', 't_rubrique.chap_code')
            ->leftJoin('compte', 'compte.no_compte',       '=', 't_detail_journal.cpt_no_compte')
            ->whereBetween('t_detail_journal.j_detail_date', [$dateDebut, $dateFin]);

        if ($compteId) {
            $baseQuery->where('t_detail_journal.cpt_no_compte', $compteId);
        }

        // Totaux globaux
        $totauxGlobaux = (clone $baseQuery)
            ->selectRaw("
                SUM(CASE WHEN chapitre.chap_code LIKE 'A%'
                    THEN t_detail_journal.j_detail_montant ELSE 0 END) as total_recettes,
                SUM(CASE WHEN chapitre.chap_code LIKE 'B%'
                    THEN t_detail_journal.j_detail_montant ELSE 0 END) as total_depenses
            ")
            ->first();

        $totalRecettesGlobal = $totauxGlobaux->total_recettes ?? 0;
        $totalDepensesGlobal = $totauxGlobaux->total_depenses ?? 0;

        // Totaux sur TOUTE la période (sans filtre date)
        $totauxGlobaux = \DB::table('t_detail_journal as d')
            ->join('t_rubrique as r', 'r.rubrique_id', '=', 'd.rub_rubrique_id')
            ->join('chapitre   as c', 'c.chap_code',   '=', 'r.chap_code')
            ->when($compteId, fn($q) => $q->where('d.cpt_no_compte', $compteId))
            ->selectRaw("
                SUM(CASE WHEN c.chap_code LIKE 'A%' THEN d.j_detail_montant ELSE 0 END) as total_recettes,
                SUM(CASE WHEN c.chap_code LIKE 'B%' THEN d.j_detail_montant ELSE 0 END) as total_depenses
            ")
            ->first();

        $totalRecettesGlobal = $totauxGlobaux->total_recettes ?? 0;
        $totalDepensesGlobal = $totauxGlobaux->total_depenses ?? 0;

        // Écritures paginées
        $details = (clone $baseQuery)
            ->selectRaw('
                t_detail_journal.j_detail_date,
                t_detail_journal.j_detail_libelle,
                t_detail_journal.j_detail_mode_paie,
                t_detail_journal.j_detail_montant,
                t_detail_journal.rub_rubrique_id,
                t_detail_journal.cpt_no_compte,
                t_journal.journal_mois,
                chapitre.chap_code,
                compte.libelle_compte
            ')
            ->orderBy('t_detail_journal.j_detail_date')
            ->paginate(30)
            ->withQueryString();

        $comptes = \App\Models\Compte::actif()->get();

        return view('finances.recap.par_compte', compact(
            'details', 'comptes', 'compteId',
            'dateDebut', 'dateFin',
            'totalRecettesGlobal', 'totalDepensesGlobal'
        ));
    }
    
        
    // PDF Récapitulatif par rubrique
    public function pdfRubrique(Request $request)
    {
        $mois  = $request->get('mois',  (int)date('m'));
        $annee = $request->get('annee', (int)date('Y'));
    
        $donnees = \DB::table('t_detail_journal')
            ->join('t_journal',  't_journal.journal_id',   '=', 't_detail_journal.jrl_journal_id')
            ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
            ->join('chapitre',   'chapitre.chap_code',     '=', 't_rubrique.chap_code')
            ->where('t_journal.journal_mois',  $mois)
            ->where('t_journal.journal_annee', $annee)
            ->selectRaw('t_rubrique.rubrique_id, t_rubrique.rubrique_libelle, t_rubrique.chap_code, chapitre.chap_libelle, SUM(j_detail_montant) as total')
            ->groupBy('t_rubrique.rubrique_id', 't_rubrique.rubrique_libelle', 't_rubrique.chap_code', 'chapitre.chap_libelle')
            ->orderBy('t_rubrique.chap_code')
            ->orderBy('t_rubrique.rubrique_id')
            ->get()
            ->keyBy('rubrique_id');
    
        $rubriques = \DB::table('t_rubrique')
            ->join('chapitre', 'chapitre.chap_code', '=', 't_rubrique.chap_code')
            ->select('t_rubrique.*', 'chapitre.chap_libelle')
            ->orderBy('t_rubrique.chap_code')
            ->orderBy('t_rubrique.rubrique_id')
            ->get()
            ->groupBy('chap_code');
    
        $totalRecettes = $donnees->filter(fn($d) => str_starts_with($d->chap_code, 'A'))->sum('total');
        $totalDepenses = $donnees->filter(fn($d) => str_starts_with($d->chap_code, 'B'))->sum('total');
    
        $moisLabel = \App\Models\Mois::find($mois)?->libelle_mois_fr ?? $mois;
    
        $pdf = Pdf::loadView('pdf.recap_rubrique', compact(
            'donnees', 'rubriques', 'mois', 'annee', 'moisLabel',
            'totalRecettes', 'totalDepenses'
        ))->setPaper('a4', 'portrait');
    
        return $pdf->download("recap_rubrique_{$annee}_" . str_pad($mois, 2, '0', STR_PAD_LEFT) . ".pdf");
    }
    
    
    // PDF Récapitulatif par chapitre
    public function pdfChapitre(Request $request)
    {
        $mois  = $request->get('mois',  (int)date('m'));
        $annee = $request->get('annee', (int)date('Y'));
    
        $donnees = \DB::table('t_detail_journal')
            ->join('t_journal',  't_journal.journal_id',   '=', 't_detail_journal.jrl_journal_id')
            ->join('t_rubrique', 't_rubrique.rubrique_id', '=', 't_detail_journal.rub_rubrique_id')
            ->join('chapitre',   'chapitre.chap_code',     '=', 't_rubrique.chap_code')
            ->where('t_journal.journal_mois',  $mois)
            ->where('t_journal.journal_annee', $annee)
            ->selectRaw('chapitre.chap_code, chapitre.chap_libelle, SUM(t_detail_journal.j_detail_montant) as total')
            ->groupBy('chapitre.chap_code', 'chapitre.chap_libelle')
            ->orderBy('chapitre.chap_code')
            ->get();
    
        $moisLabel = \App\Models\Mois::find($mois)?->libelle_mois_fr ?? $mois;
    
        $pdf = Pdf::loadView('pdf.recap_chapitre', compact(
            'donnees', 'mois', 'annee', 'moisLabel'
        ))->setPaper('a4', 'portrait');
    
        return $pdf->download("recap_chapitre_{$annee}_" . str_pad($mois, 2, '0', STR_PAD_LEFT) . ".pdf");
    }
    
    
    // PDF Journal par compte
    public function pdfCompte(Request $request)
    {
        $compteId  = $request->get('compte');
        $dateDebut = $request->get('date_debut', now()->startOfYear()->format('Y-m-d'));
        $dateFin   = $request->get('date_fin',   now()->format('Y-m-d'));

        $details = \DB::table('t_detail_journal as d')
            ->join('t_journal  as j', 'j.journal_id',  '=', 'd.jrl_journal_id')
            ->join('t_rubrique as r', 'r.rubrique_id', '=', 'd.rub_rubrique_id')
            ->join('chapitre   as c', 'c.chap_code',   '=', 'r.chap_code')
            ->leftJoin('compte as cp', 'cp.no_compte',  '=', 'd.cpt_no_compte')
            ->whereBetween('d.j_detail_date', [$dateDebut, $dateFin])
            ->when($compteId, fn($q) => $q->where('d.cpt_no_compte', $compteId))
            ->orderBy('c.chap_code')
            ->orderBy('d.j_detail_date')
            ->selectRaw("
                d.j_detail_date,
                d.j_detail_libelle,
                d.j_detail_mode_paie,
                d.j_detail_montant,
                d.rub_rubrique_id,
                d.cpt_no_compte,
                c.chap_code,
                c.chap_libelle,
                cp.libelle_compte
            ")
            ->get();  // ← get() pas paginate()

        $libelleCompte = $compteId
            ? \DB::table('compte')->where('no_compte', $compteId)->value('libelle_compte')
            : null;

        $totaux = \DB::table('t_detail_journal as d')
            ->join('t_journal  as j', 'j.journal_id',  '=', 'd.jrl_journal_id')
            ->join('t_rubrique as r', 'r.rubrique_id', '=', 'd.rub_rubrique_id')
            ->join('chapitre   as c', 'c.chap_code',   '=', 'r.chap_code')
            ->whereBetween('d.j_detail_date', [$dateDebut, $dateFin])
            ->when($compteId, fn($q) => $q->where('d.cpt_no_compte', $compteId))
            ->selectRaw("
                SUM(CASE WHEN c.chap_code LIKE 'A%' THEN d.j_detail_montant ELSE 0 END) as total_recettes,
                SUM(CASE WHEN c.chap_code LIKE 'B%' THEN d.j_detail_montant ELSE 0 END) as total_depenses
            ")
            ->first();

        $totalRecettesGlobal = $totaux->total_recettes ?? 0;
        $totalDepensesGlobal = $totaux->total_depenses ?? 0;

        // Totaux sur TOUTE la période (sans filtre date)
        $totauxGlobaux = \DB::table('t_detail_journal as d')
            ->join('t_rubrique as r', 'r.rubrique_id', '=', 'd.rub_rubrique_id')
            ->join('chapitre   as c', 'c.chap_code',   '=', 'r.chap_code')
            ->when($compteId, fn($q) => $q->where('d.cpt_no_compte', $compteId))
            ->selectRaw("
                SUM(CASE WHEN c.chap_code LIKE 'A%' THEN d.j_detail_montant ELSE 0 END) as total_recettes,
                SUM(CASE WHEN c.chap_code LIKE 'B%' THEN d.j_detail_montant ELSE 0 END) as total_depenses
            ")
            ->first();

        $totalRecettesGlobal = $totauxGlobaux->total_recettes ?? 0;
        $totalDepensesGlobal = $totauxGlobaux->total_depenses ?? 0;

        $pdf = Pdf::loadView('pdf.recap_compte', compact(
            'details', 'compteId', 'libelleCompte',
            'dateDebut', 'dateFin',
            'totalRecettesGlobal', 'totalDepensesGlobal'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("journal_compte_{$dateDebut}_{$dateFin}.pdf");
    }
    
}
