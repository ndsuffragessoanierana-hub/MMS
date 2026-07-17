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
// FICHIER : app/Http/Controllers/Finance/ChapitreController.php
// =============================================================================

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Chapitre;
use Illuminate\Http\Request;

class ChapitreController extends Controller
{
    // public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $chapitres = Chapitre::withCount('rubriques')
            ->orderBy('chap_code')
            ->get();

        return view('finances.chapitres.index', compact('chapitres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'chap_code'    => 'required|string|max:20|unique:chapitres,chap_code',
            'chap_libelle' => 'required|string|max:200',
        ]);

        Chapitre::create(['chap_code' => strtoupper($request->chap_code), 'chap_libelle' => $request->chap_libelle]);
        return back()->with('success', 'Chapitre créé.');
    }

    public function update(Request $request, Chapitre $chapitre)
    {
        $request->validate([
            'chap_code'    => 'required|string|max:20',
            'chap_libelle' => 'required|string|max:200',
            'actuel'       => 'required|in:O,N',
        ]);

        $ancienCode = $chapitre->chap_code;
        $nouveauCode = strtoupper($request->chap_code);

        if ($nouveauCode !== $ancienCode && $chapitre->rubriques()->exists()) {
            return back()->with('error', 'Impossible : des rubriques utilisent ce chapitre.');
        }

        if ($nouveauCode !== $ancienCode) {
            $chapitre->delete();
            Chapitre::create([
                'chap_code'    => $nouveauCode,
                'chap_libelle' => $request->chap_libelle,
                'actuel'       => $request->actuel,
            ]);
        } else {
            $chapitre->update([
                'chap_libelle' => $request->chap_libelle,
                'actuel'       => $request->actuel,
            ]);
        }

        return back()->with('success', 'Chapitre mis à jour.');
    }

    public function destroy(Chapitre $chapitre)
    {
        if ($chapitre->rubriques()->exists()) {
            return back()->with('error', 'Impossible : ce chapitre a des rubriques associées.');
        }
        $chapitre->delete();
        return back()->with('success', 'Chapitre supprimé.');
    }
}
