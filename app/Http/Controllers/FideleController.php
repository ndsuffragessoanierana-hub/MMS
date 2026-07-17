<?php
// =============================================================================
// ECAR — Module Fidèles : CRUD complet
// Liber Status Animarum (Registre des fidèles)
// =============================================================================
// Fichiers à créer :
//   app/Http/Controllers/FideleController.php
//   app/Http/Requests/StoreFideleRequest.php
//   app/Http/Requests/UpdateFideleRequest.php
//   routes/web.php  (extrait)
//   resources/views/fideles/index.blade.php
//   resources/views/fideles/create.blade.php
//   resources/views/fideles/edit.blade.php
//   resources/views/fideles/show.blade.php
//   resources/views/fideles/_form.blade.php
// =============================================================================


// =============================================================================
// FICHIER : app/Http/Controllers/FideleController.php
// =============================================================================

namespace App\Http\Controllers;

use App\Models\{Fidele, Faritra, Apv, Fikambanana};
use App\Http\Requests\{StoreFideleRequest, UpdateFideleRequest};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FideleController extends Controller
{
    // Middleware : seuls les utilisateurs connectés accèdent
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    // ------------------------------------------------------------------
    // INDEX — Liste des fidèles avec filtres et pagination
    // ------------------------------------------------------------------
    public function index(Request $request)
    {
        $query = Fidele::with(['faritra', 'apv'])
            ->orderBy('nom')
            ->orderBy('prenom');

        // Filtres
        if ($request->filled('recherche')) {
            $query->recherche($request->recherche);
        }
        if ($request->filled('faritra_id')) {
            $query->faritra($request->faritra_id);
        }
        if ($request->filled('apv_id')) {
            $query->apv($request->apv_id);
        }
        if ($request->filled('sexe')) {
            $query->where('sexe', $request->sexe);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->boolean('quitte')) {
            $query->partis();
        } else {
            $query->actifs();
        }

        $fideles  = $query->paginate(25)->withQueryString();
        $faritraS = Faritra::ordonne()->get();
        $apvs     = $request->filled('faritra_id')
            ? Apv::where('idfaritra', $request->faritra_id)->get()
            : collect();

        // Statistiques rapides
        $stats = [
            'total'   => Fidele::actifs()->count(),
            'hommes'  => Fidele::actifs()->masculin()->count(),
            'femmes'  => Fidele::actifs()->feminin()->count(),
            'partis'  => Fidele::partis()->count(),
            'decedes' => Fidele::decedes()->count(),
        ];

        return view('fideles.index', compact('fideles', 'faritraS', 'apvs', 'stats'));
    }

    // ------------------------------------------------------------------
    // CREATE — Formulaire de création
    // ------------------------------------------------------------------
    public function create()
    {
        $this->authorize_role('ajout');

        $faritraS = Faritra::ordonne()->get();
        $apvs     = collect();
        $fidele   = new Fidele();
        $matricule = $this->genererMatricule();

        return view('fideles.create', compact('faritraS', 'apvs', 'fidele', 'matricule'));
    }

    // ------------------------------------------------------------------
    // STORE — Enregistrement d'un nouveau fidèle
    // ------------------------------------------------------------------
    public function store(StoreFideleRequest $request)
    {
        $this->authorize_role('ajout');

        $fidele = Fidele::create($request->validated());

        return redirect()
            ->route('fideles.show', $fidele->matricule)
            ->with('success', "Le fidèle {$fidele->nom_complet} a été enregistré avec succès.");
    }

    // ------------------------------------------------------------------
    // SHOW — Fiche complète du fidèle
    // ------------------------------------------------------------------
    public function show(Fidele $fidele)
    {
        $fidele->load(['faritra', 'apv', 'fikambananas.pivot']);
        return view('fideles.show', compact('fidele'));
    }

    // ------------------------------------------------------------------
    // EDIT — Formulaire de modification
    // ------------------------------------------------------------------
    public function edit(Fidele $fidele)
    {
        $this->authorize_role('modif');

        $faritraS = Faritra::ordonne()->get();
        $apvs     = $fidele->idfaritra
            ? Apv::where('idfaritra', $fidele->idfaritra)->get()
            : collect();

        return view('fideles.edit', compact('fidele', 'faritraS', 'apvs'));
    }

    // ------------------------------------------------------------------
    // UPDATE — Mise à jour du fidèle
    // ------------------------------------------------------------------
    public function update(UpdateFideleRequest $request, Fidele $fidele)
    {
        $this->authorize_role('modif');

        $fidele->update($request->validated());

        return redirect()
            ->route('fideles.show', $fidele->matricule)
            ->with('success', "La fiche de {$fidele->nom_complet} a été mise à jour.");
    }

    // ------------------------------------------------------------------
    // DESTROY — Suppression logique (marque comme parti)
    // ------------------------------------------------------------------
    public function destroy(Fidele $fidele)
    {
        $this->authorize_role('suppr');

        // Suppression logique : on marque comme "parti"
        $fidele->update(['quitte' => 'O']);

        return redirect()
            ->route('fideles.index')
            ->with('success', "Le fidèle {$fidele->nom_complet} a été marqué comme parti.");
    }

    // ------------------------------------------------------------------
    // API : APV selon Faritra (pour le select dynamique AJAX)
    // ------------------------------------------------------------------
    public function apvParFaritra(int $idfaritra)
    {
        $apvs = Apv::where('idfaritra', $idfaritra)
            ->orderBy('libelle_apv')
            ->get(['idapv', 'libelle_apv']);

        return response()->json($apvs);
    }

    // ------------------------------------------------------------------
    // EXPORT Excel (liste des fidèles)
    // ------------------------------------------------------------------
    public function export(Request $request)
    {
        // Nécessite : composer require maatwebsite/excel
        // return Excel::download(new FidelesExport($request->all()), 'fideles.xlsx');
        abort(501, 'Export Excel : installez maatwebsite/excel');
    }

    // ------------------------------------------------------------------
    // Helpers privés
    // ------------------------------------------------------------------
    private function genererMatricule(): string
    {
        $annee    = date('Y');
        $dernier  = Fidele::where('matricule', 'like', "F{$annee}%")
            ->orderByDesc('matricule')
            ->value('matricule');
        $sequence = $dernier ? (int)substr($dernier, 5) + 1 : 1;
        return sprintf('F%s%04d', $annee, $sequence);
    }

    private function authorize_role(string $role): void
    {
        $roles = ['lecture' => 0, 'modif' => 1, 'ajout' => 2, 'suppr' => 3, 'admin' => 4];
        $user  = auth()->user();
        if (($roles[$user->role] ?? -1) < ($roles[$role] ?? 99)) {
            abort(403, 'Action non autorisée.');
        }
    }
}
