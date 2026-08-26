<?php

namespace App\Http\Controllers\Finance;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\{TJournal, TDetailJournal, TRubrique, Compte, Mois};
use App\Http\Requests\{StoreJournalRequest, StoreDetailJournalRequest};
use App\Services\JournalSoldeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    // public function __construct() { $this->middleware('auth'); }

    // ------------------------------------------------------------------
    // INDEX — Liste de tous les journaux mensuels
    // ------------------------------------------------------------------
    public function index(Request $request)
    {
        $annee = $request->get('annee', date('Y'));

        $journals = TJournal::with(['mois', 'createur'])
            ->annee($annee)
            ->orderBy('journal_mois')
            ->get();

        $totaux = TJournal::annee($annee)->selectRaw('
            SUM(journal_solde_bni)    as total_bni,
            SUM(journal_solde_bfv)    as total_bfv,
            SUM(journal_solde_caisse) as total_caisse
        ')->first();

        $annees = TJournal::selectRaw('DISTINCT journal_annee')
            ->orderByDesc('journal_annee')
            ->pluck('journal_annee');

        return view('finances.journal.index', compact('journals', 'annee', 'totaux', 'annees'));
    }

    // ------------------------------------------------------------------
    // CREATE — Ouvrir un nouveau journal mensuel
    // ------------------------------------------------------------------
    public function create()
    {
        $this->authorize_role('ajout');

        $dernier = TJournal::orderByDesc('journal_annee')
            ->orderByDesc('journal_mois')
            ->first();

        $moisSuggere = $dernier
            ? ($dernier->journal_mois == 12
                ? ['mois' => 1,  'annee' => $dernier->journal_annee + 1]
                : ['mois' => $dernier->journal_mois + 1, 'annee' => $dernier->journal_annee])
            : ['mois' => (int)date('m'), 'annee' => (int)date('Y')];

        $mois = Mois::all();

        return view('finances.journal.create', compact('moisSuggere', 'mois', 'dernier'));
    }

    // ------------------------------------------------------------------
    // STORE — Créer le journal mensuel
    // ------------------------------------------------------------------
    public function store(StoreJournalRequest $request)
    {
        $this->authorize_role('ajout');

        $journal = TJournal::create([
            'journal_mois'          => $request->journal_mois,
            'journal_annee'         => $request->journal_annee,
            'journal_solde_bni'     => $request->journal_solde_bni     ?? 0,
            'journal_solde_bfv'     => $request->journal_solde_bfv     ?? 0,
            'journal_solde_caisse'  => $request->journal_solde_caisse  ?? 0,
            'user_id'               => auth()->id(),
        ]);

        return redirect()
            ->route('journals.show', $journal->journal_id)
            ->with('success', "Journal {$journal->periode} ouvert avec succès.");
    }

    // ------------------------------------------------------------------
    // SHOW — Livre journal d'un mois (toutes les écritures)
    // ------------------------------------------------------------------
    public function show(TJournal $journal)
    {
        $journal->load(['mois', 'createur']);

        $details = TDetailJournal::with(['rubrique.chapitre', 'compte'])
            ->where('jrl_journal_id', $journal->journal_id)
            ->orderBy('j_detail_date')
            ->orderBy('j_detail_numero')
            ->get();

        $totauxChapitres = $details->groupBy('rubrique.chap_code')->map(function ($items) {
            return [
                'libelle' => $items->first()?->rubrique?->chapitre?->chap_libelle,
                'total'   => $items->sum('j_detail_montant'),
            ];
        });

        $totalRecettes = $details->filter(fn($d) => str_starts_with($d->rubrique?->chap_code ?? '', 'A'))
            ->sum('j_detail_montant');
        $totalDepenses = $details->filter(fn($d) => str_starts_with($d->rubrique?->chap_code ?? '', 'B'))
            ->sum('j_detail_montant');

        $rubriques = TRubrique::with('chapitre')->orderBy('rubrique_id')->get();
        $comptes   = Compte::actif()->get();

        return view('finances.journal.show', compact(
            'journal', 'details', 'totauxChapitres',
            'totalRecettes', 'totalDepenses', 'rubriques', 'comptes'
        ));
    }

    // ------------------------------------------------------------------
    // UPDATE — Modifier les soldes du journal
    // ------------------------------------------------------------------
    public function update(StoreJournalRequest $request, TJournal $journal)
    {
        $this->authorize_role('modif');

        $journal->update([
            'journal_solde_bni'    => $request->journal_solde_bni    ?? $journal->journal_solde_bni,
            'journal_solde_bfv'    => $request->journal_solde_bfv    ?? $journal->journal_solde_bfv,
            'journal_solde_caisse' => $request->journal_solde_caisse ?? $journal->journal_solde_caisse,
        ]);

        return back()->with('success', 'Soldes mis à jour.');
    }

    // ------------------------------------------------------------------
    // DESTROY — Supprimer un journal (et ses écritures en cascade)
    // ------------------------------------------------------------------
    public function destroy(TJournal $journal)
    {
        $this->authorize_role('suppr');
        $periode = $journal->periode;
        $journal->delete();

        return redirect()->route('journals.index')
            ->with('success', "Journal {$periode} supprimé.");
    }

    // ------------------------------------------------------------------
    // Écritures (sous-ressource)
    // ------------------------------------------------------------------

    // Ajouter une écriture
    public function storeEcriture(StoreDetailJournalRequest $request, TJournal $journal)
    {
        $this->authorize_role('ajout');

        DB::transaction(function () use ($request, $journal) {
            $detail = TDetailJournal::create([
                'j_detail_date'      => $request->j_detail_date,
                'j_detail_libelle'   => $request->j_detail_libelle,
                'j_detail_mode_paie' => $request->j_detail_mode_paie,
                'j_detail_montant'   => $request->j_detail_montant,
                'jrl_journal_id'     => $journal->journal_id,
                'rub_rubrique_id'    => $request->rub_rubrique_id,
                'cpt_no_compte'      => $request->cpt_no_compte ?: null,
            ]);

            app(JournalSoldeService::class)->enregistrerEcriture(
                $journal->journal_id,
                $detail->j_detail_mode_paie,
                $detail->rub_rubrique_id,
                $detail->j_detail_montant
            );
        });

        return back()->with('success', 'Écriture ajoutée.');
    }

    // Modifier une écriture
    public function updateEcriture(StoreDetailJournalRequest $request, TDetailJournal $detail)
    {
        $this->authorize_role('modif');

        DB::transaction(function () use ($request, $detail) {
            $service = app(JournalSoldeService::class);

            // Annule l'effet de l'ancienne écriture avant de la modifier
            $service->annulerEcriture(
                $detail->jrl_journal_id,
                $detail->j_detail_mode_paie,
                $detail->rub_rubrique_id,
                $detail->j_detail_montant
            );

            $detail->update($request->validated());

            // Applique l'effet de la nouvelle écriture
            $service->enregistrerEcriture(
                $detail->jrl_journal_id,
                $detail->j_detail_mode_paie,
                $detail->rub_rubrique_id,
                $detail->j_detail_montant
            );
        });

        return back()->with('success', 'Écriture modifiée.');
    }

    // Supprimer une écriture
    public function destroyEcriture(TDetailJournal $detail)
    {
        $journalId = $detail->jrl_journal_id;

        DB::transaction(function () use ($detail, $journalId) {
            app(JournalSoldeService::class)->annulerEcriture(
                $journalId,
                $detail->j_detail_mode_paie,
                $detail->rub_rubrique_id,
                $detail->j_detail_montant
            );

            $detail->delete();
        });

        return redirect()->route('finances.journals.show', $journalId)
            ->with('success', 'Écriture supprimée.');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------
    private function authorize_role(string $role): void
    {
        $niveaux = ['lecture' => 0, 'modif' => 1, 'ajout' => 2, 'suppr' => 3, 'admin' => 4];
        if (($niveaux[auth()->user()->role] ?? -1) < ($niveaux[$role] ?? 99)) {
            abort(403, 'Action non autorisée.');
        }
    }

    public function exportPdf(TJournal $journal)
    {
        $journal->load('mois');

        $periode = $journal->mois?->libelle_mois_fr . ' ' . $journal->journal_annee;

        $details = \DB::table('t_detail_journal as d')
            ->join('t_rubrique as r',  'r.rubrique_id', '=', 'd.rub_rubrique_id')
            ->join('chapitre   as c',  'c.chap_code',   '=', 'r.chap_code')
            ->where('d.jrl_journal_id', $journal->journal_id)
            ->where(function($q) {
                $q->where('c.chap_code', 'like', 'A%')
                ->orWhere('c.chap_code', 'like', 'B%');
            })
            ->orderBy('d.j_detail_date')
            ->orderBy('d.j_detail_numero')
            ->selectRaw("
                d.j_detail_numero,
                d.j_detail_date,
                d.j_detail_libelle,
                d.rub_rubrique_id,
                d.j_detail_mode_paie,
                d.j_detail_montant,
                r.chap_code,
                c.chap_libelle,
                CASE WHEN c.chap_code LIKE 'A%' THEN d.j_detail_montant ELSE 0 END AS recette_g,
                CASE WHEN c.chap_code LIKE 'B%' THEN d.j_detail_montant ELSE 0 END AS depense_g,
                CASE WHEN c.chap_code LIKE 'A%' AND d.j_detail_mode_paie = 'ESP' THEN d.j_detail_montant ELSE 0 END AS recette_num,
                CASE WHEN c.chap_code LIKE 'B%' AND d.j_detail_mode_paie = 'ESP' THEN d.j_detail_montant ELSE 0 END AS depense_num,
                CASE WHEN c.chap_code LIKE 'A%' AND d.j_detail_mode_paie = 'BFV' THEN d.j_detail_montant ELSE 0 END AS recette_bfv,
                CASE WHEN c.chap_code LIKE 'B%' AND d.j_detail_mode_paie = 'BFV' THEN d.j_detail_montant ELSE 0 END AS depense_bfv,
                CASE WHEN c.chap_code LIKE 'A%' AND d.j_detail_mode_paie = 'BNI' THEN d.j_detail_montant ELSE 0 END AS recette_bni,
                CASE WHEN c.chap_code LIKE 'B%' AND d.j_detail_mode_paie = 'BNI' THEN d.j_detail_montant ELSE 0 END AS depense_bni
            ")
            ->get();

        $recap = \DB::table('t_detail_journal as d')
            ->join('t_rubrique as r', 'r.rubrique_id', '=', 'd.rub_rubrique_id')
            ->join('chapitre   as c', 'c.chap_code',   '=', 'r.chap_code')
            ->where('d.jrl_journal_id', $journal->journal_id)
            ->where(function($q) {
                $q->where('c.chap_code', 'like', 'A%')
                ->orWhere('c.chap_code', 'like', 'B%');
            })
            ->groupBy('r.rubrique_id', 'r.rubrique_libelle', 'c.chap_code', 'c.chap_libelle')
            ->orderBy('c.chap_code')
            ->orderBy('r.rubrique_id')
            ->selectRaw("
                r.rubrique_id,
                r.rubrique_libelle,
                c.chap_code,
                c.chap_libelle,
                SUM(CASE WHEN c.chap_code LIKE 'A%' THEN d.j_detail_montant ELSE 0 END) AS recette,
                SUM(CASE WHEN c.chap_code LIKE 'B%' THEN d.j_detail_montant ELSE 0 END) AS depense
            ")
            ->get();

        $soldeCourant = (object)[
            'journal_solde_bni'    => $journal->journal_solde_bni,
            'journal_solde_bfv'    => $journal->journal_solde_bfv,
            'journal_solde_caisse' => $journal->journal_solde_caisse,
            'total'                => $journal->solde_total,
        ];

        $journalPrecedent = TJournal::where(function($q) use ($journal) {
                if ($journal->journal_mois == 1) {
                    $q->where('journal_mois',  12)
                    ->where('journal_annee', $journal->journal_annee - 1);
                } else {
                    $q->where('journal_mois',  $journal->journal_mois - 1)
                    ->where('journal_annee', $journal->journal_annee);
                }
            })->first();

        $soldePrecedent = (object)[
            'journal_solde_bni'    => $journalPrecedent?->journal_solde_bni    ?? 0,
            'journal_solde_bfv'    => $journalPrecedent?->journal_solde_bfv    ?? 0,
            'journal_solde_caisse' => $journalPrecedent?->journal_solde_caisse ?? 0,
            'total'                => $journalPrecedent?->solde_total           ?? 0,
        ];

        $totalRecettes = $recap->sum('recette');
        $totalDepenses = $recap->sum('depense');

        $pdf = Pdf::loadView('pdf.journal', compact(
            'journal',
            'periode',
            'details',
            'recap',
            'soldeCourant',
            'soldePrecedent'
        ))->setPaper('a4', 'landscape');

        $filename = 'journal_'
            . $journal->journal_annee . '_'
            . str_pad($journal->journal_mois, 2, '0', STR_PAD_LEFT)
            . '.pdf';

        return $pdf->download($filename);
    }
}