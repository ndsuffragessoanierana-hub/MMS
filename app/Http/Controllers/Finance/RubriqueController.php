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
// FICHIER : app/Http/Controllers/Finance/RubriqueController.php
// =============================================================================

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\{TRubrique, Chapitre};
use App\Http\Requests\StoreRubriqueRequest;

// Dans RubriqueController::index()
$rubriques = TRubrique::with('chapitre')
    ->where(function($q) {
        $q->where('rubrique_id', 'like', 'A%')
        ->orWhere('rubrique_id', 'like', 'B%');
    })
    ->withCount('detailJournals')
    ->orderBy('chap_code')
    ->orderBy('rubrique_id')
    ->get()
    ->groupBy('chap_code');

class RubriqueController extends Controller
{
    // public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $rubriques = TRubrique::with('chapitre')
            ->where(function($q) {
                $q->where('rubrique_id', 'like', 'A%')
                ->orWhere('rubrique_id', 'like', 'B%');
            })
            ->orderBy('chap_code')
            ->orderBy('rubrique_id')
            ->get()
            ->groupBy('chap_code'); // groupement par chapitre


        $chapitres = Chapitre::actuel()->orderBy('chap_code')->get();

        return view('finances.rubriques.index', compact('rubriques', 'chapitres'));
    }

    public function store(StoreRubriqueRequest $request)
    {
        TRubrique::create([
            'rubrique_id'      => strtoupper($request->rubrique_id),
            'rubrique_libelle' => $request->rubrique_libelle,
            'chap_code'        => $request->chap_code,
            'date_saisie'      => now()->toDateString(),
            'user_id'          => auth()->id(),
        ]);

        return back()->with('success', 'Rubrique créée.');
    }

    public function update(StoreRubriqueRequest $request, TRubrique $rubrique)
    {
        $ancienId = $rubrique->rubrique_id;
        $nouveauId = strtoupper($request->rubrique_id ?? $ancienId);

        // Si le code change
        if ($nouveauId !== $ancienId) {

            // Vérifier qu'aucune écriture n'utilise cette rubrique
            if ($rubrique->detailJournals()->exists()) {
                return back()
                    ->withInput()
                    ->with('error', 'Impossible de modifier le code : des écritures utilisent cette rubrique.');
            }

            $rubrique->delete();

            TRubrique::create([
                'rubrique_id'      => $nouveauId,
                'rubrique_libelle' => $request->rubrique_libelle,
                'chap_code'        => $request->chap_code,
                'date_saisie'      => now()->toDateString(),
                'user_id'          => auth()->id(),
            ]);

        } else {
            // Code inchangé → simple update
            $rubrique->update([
                'rubrique_libelle' => $request->rubrique_libelle,
                'chap_code'        => $request->chap_code,
            ]);
        }

        return back()->with('success', 'Rubrique mise à jour.');
    }

    public function destroy(TRubrique $rubrique)
    {
        if ($rubrique->detailJournals()->exists()) {
            return back()->with('error', 'Impossible : cette rubrique est utilisée dans le journal.');
        }
        $rubrique->delete();
        return back()->with('success', 'Rubrique supprimée.');
    }
}
