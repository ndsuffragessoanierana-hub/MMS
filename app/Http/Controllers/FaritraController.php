<?php
// =============================================================================
// ECAR — Controllers manquants
// Faritra · Apv · Fikambanana · TypeFitaovana · Agenda
// =============================================================================


// =============================================================================
// FICHIER : app/Http/Controllers/FaritraController.php
// =============================================================================

namespace App\Http\Controllers;

use App\Models\Faritra;
use Illuminate\Http\Request;

class FaritraController extends Controller
{
    public function index()
    {
        $faritras = Faritra::ordonne()->withCount('fideles')->get();
        return view('faritra.index', compact('faritras'));
    }

    public function create()
    {
        $this->authorize_role('ajout');
        return view('faritra.create');
    }

    public function store(Request $request)
    {
        $this->authorize_role('ajout');

        $validated = $request->validate([
            'libelle_faritra'   => 'required|string|max:100',
            'st_patron'         => 'nullable|string|max:100',
            'sigle'             => 'nullable|string|max:20',
            'num_ordre_faritra' => 'nullable|integer',
        ]);

        $faritra = Faritra::create($validated);

        return redirect()->route('faritraS.index')
            ->with('success', "Faritra « {$faritra->libelle_faritra} » créé avec succès.");
    }

    public function show(Faritra $faritra)
    {
        $faritra->load('apvs');
        $faritra->loadCount('fideles');
        return view('faritra.show', ['faritra' => $faritra]);
    }

    public function edit(Faritra $faritra)
    {
        $this->authorize_role('modif');
        return view('faritra.edit', ['faritra' => $faritra]);
    }

    public function update(Request $request, Faritra $faritra)
    {
        $this->authorize_role('modif');

        $validated = $request->validate([
            'libelle_faritra'   => 'required|string|max:100',
            'st_patron'         => 'nullable|string|max:100',
            'sigle'             => 'nullable|string|max:20',
            'num_ordre_faritra' => 'nullable|integer',
        ]);

        $faritra->update($validated);

        return redirect()->route('faritraS.index')
            ->with('success', 'Faritra mis à jour.');
    }

    public function destroy(Faritra $faritra)
    {
        $this->authorize_role('suppr');

        if ($faritraS->fideles()->exists()) {
            return back()->with('error', 'Impossible : des fidèles sont rattachés à ce Faritra.');
        }

        $nom = $faritra->libelle_faritra;
        $faritra->delete();

        return redirect()->route('faritraS.index')
            ->with('success', "Faritra « {$nom} » supprimé.");
    }

    private function authorize_role(string $role): void
    {
        $niveaux = ['lecture' => 0, 'modif' => 1, 'ajout' => 2, 'suppr' => 3, 'admin' => 4];
        if (($niveaux[auth()->user()->role] ?? -1) < ($niveaux[$role] ?? 99)) {
            abort(403);
        }
    }
}
