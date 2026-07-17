<?php
// =============================================================================
// ECAR — Étape 6 (finale) :
//   • CRUD Inventaire (Fitaovana)
//   • Layout principal + Navigation
//   • Dashboard d'accueil (graphiques, soldes, statistiques)
//   • Guide d'installation complet
// =============================================================================


// =============================================================================
// FICHIER : app/Http/Controllers/InventaireController.php
// =============================================================================

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\{Fitaovana, TypeFitaovana};
use Illuminate\Http\Request;

class InventaireController extends Controller
{
    // public function __construct() { $this->middleware('auth'); }

    // ------------------------------------------------------------------
    // INDEX
    // ------------------------------------------------------------------
    public function index(Request $request)
    {
        $query = Fitaovana::with('typeFitaovana')->orderBy('denomination');

        if ($request->filled('recherche')) {
            $query->recherche($request->recherche);
        }
        if ($request->filled('type_id')) {
            $query->parType($request->type_id);
        }

        $fitaovanas = $query->paginate(20)->withQueryString();
        $types      = TypeFitaovana::orderBy('libelle_type_fitaovana')->get();

        // Statistiques
        $stats = [
            'total'          => Fitaovana::count(),
            'valeur_totale'  => Fitaovana::sum(\DB::raw('COALESCE(valeur_acquisition,0) * qte_achetee')),
            'nb_types'       => TypeFitaovana::count(),
        ];

        return view('inventaire.index', compact('fitaovanas', 'types', 'stats'));
    }

    // ------------------------------------------------------------------
    // CREATE
    // ------------------------------------------------------------------
    public function create()
    {
        $this->authorize_role('ajout');
        $types    = TypeFitaovana::orderBy('libelle_type_fitaovana')->get();
        $fitaovana = new Fitaovana();
        return view('inventaire.create', compact('types', 'fitaovana'));
    }

    // ------------------------------------------------------------------
    // STORE
    // ------------------------------------------------------------------
    public function store(Request $request)
    {
        $this->authorize_role('ajout');

        $validated = $request->validate([
            'denomination'         => 'required|string|max:300',
            'reference'            => 'nullable|string|max:100',
            'date_acquisition'     => 'nullable|date',
            'valeur_acquisition'   => 'nullable|numeric|min:0',
            'qte_achetee'          => 'required|integer|min:1',
            'fournisseur'          => 'nullable|string|max:200',
            'no_inventaire'        => 'nullable|string|max:50|unique:fitaovanas,no_inventaire',
            'tf_id_type_fitaovana' => 'nullable|exists:type_fitaovanas,id_type_fitaovana',
            'remarque'             => 'nullable|string',
        ]);

        // Générer le contenu QR code
        $validated['qr_text'] = json_encode([
            'no'     => $validated['no_inventaire'] ?? 'N/A',
            'nom'    => $validated['denomination'],
            'ref'    => $validated['reference'] ?? '',
            'date'   => $validated['date_acquisition'] ?? '',
        ]);

        $fitaovana = Fitaovana::create($validated);

        return redirect()
            ->route('inventaire.show', $fitaovana->idfitaovana)
            ->with('success', "L'équipement « {$fitaovana->denomination} » a été enregistré.");
    }

    // ------------------------------------------------------------------
    // SHOW
    // ------------------------------------------------------------------
    public function show(Fitaovana $inventaire)
    {
        $inventaire->load('typeFitaovana');
        return view('inventaire.show', compact('inventaire'));
    }

    // ------------------------------------------------------------------
    // EDIT
    // ------------------------------------------------------------------
    public function edit(Fitaovana $inventaire)
    {
        $this->authorize_role('modif');
        $types = TypeFitaovana::orderBy('libelle_type_fitaovana')->get();
        return view('inventaire.edit', compact('inventaire', 'types'));
    }

    // ------------------------------------------------------------------
    // UPDATE
    // ------------------------------------------------------------------
    public function update(Request $request, Fitaovana $inventaire)
    {
        $this->authorize_role('modif');

        $validated = $request->validate([
            'denomination'         => 'required|string|max:300',
            'reference'            => 'nullable|string|max:100',
            'date_acquisition'     => 'nullable|date',
            'valeur_acquisition'   => 'nullable|numeric|min:0',
            'qte_achetee'          => 'required|integer|min:1',
            'fournisseur'          => 'nullable|string|max:200',
            'no_inventaire'        => "nullable|string|max:50|unique:fitaovanas,no_inventaire,{$inventaire->idfitaovana},idfitaovana",
            'tf_id_type_fitaovana' => 'nullable|exists:type_fitaovanas,id_type_fitaovana',
            'remarque'             => 'nullable|string',
        ]);

        $validated['qr_text'] = json_encode([
            'no'  => $validated['no_inventaire'] ?? $inventaire->no_inventaire,
            'nom' => $validated['denomination'],
            'ref' => $validated['reference'] ?? '',
        ]);

        $inventaire->update($validated);

        return redirect()
            ->route('inventaire.show', $inventaire->idfitaovana)
            ->with('success', 'Équipement mis à jour.');
    }

    // ------------------------------------------------------------------
    // DESTROY
    // ------------------------------------------------------------------
    public function destroy(Fitaovana $inventaire)
    {
        $this->authorize_role('suppr');
        $nom = $inventaire->denomination;
        $inventaire->delete();
        return redirect()->route('inventaire.index')
            ->with('success', "« {$nom} » supprimé de l'inventaire.");
    }

    private function authorize_role(string $role): void
    {
        $niveaux = ['lecture' => 0, 'modif' => 1, 'ajout' => 2, 'suppr' => 3, 'admin' => 4];
        if (($niveaux[auth()->user()->role] ?? -1) < ($niveaux[$role] ?? 99)) {
            abort(403);
        }
    }

     
    public function exportPdf(Request $request)
    {
        $fitaovanas = \DB::table('fitaovana')
            ->leftJoin('type_fitaovana', 'type_fitaovana.id_type_fitaovana', '=', 'fitaovana.tf_id_type_fitaovana')
            ->select('fitaovana.*', 'type_fitaovana.libelle_type_fitaovana as type_libelle')
            ->orderBy('type_fitaovana.libelle_type_fitaovana')
            ->orderBy('fitaovana.denomination')
            ->get();
    
        $valeurTotale = $fitaovanas->sum(fn($f) => ($f->valeur_acquisition ?? 0) * $f->qte_achetee);
    
        $pdf = Pdf::loadView('pdf.inventaire', compact('fitaovanas', 'valeurTotale'))
            ->setPaper('a4', 'landscape');
    
        return $pdf->download('inventaire_' . now()->format('Y-m-d') . '.pdf');
    }
    
}

