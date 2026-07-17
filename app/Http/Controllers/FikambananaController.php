<?php
// =============================================================================
// FICHIER : app/Http/Controllers/FikambananaController.php
// =============================================================================

namespace App\Http\Controllers;

use App\Models\{Fikambanana, Fidele, MembreRole};
use Illuminate\Http\Request;

class FikambananaController extends Controller
{
    public function index()
    {
        $fikambananas = Fikambanana::withCount('fideles')->orderBy('libelle_fikambanana')->get();
        return view('fikambanana.index', compact('fikambananas'));
    }

    public function create()
    {
        $this->authorize_role('ajout');
        return view('fikambanana.create');
    }

    public function store(Request $request)
    {
        $this->authorize_role('ajout');

        $validated = $request->validate([
            'libelle_fikambanana' => 'required|string|max:200',
            'st_patron'           => 'nullable|string|max:100',
        ]);

        $fikambanana = Fikambanana::create($validated);

        return redirect()->route('fikambananas.show', $fikambanana->idfikambanana)
            ->with('success', "« {$fikambanana->libelle_fikambanana} » créé avec succès.");
    }

    public function show(Fikambanana $fikambanana)
    {
        $fikambanana->load(['fideles' => function ($q) {
            $q->orderBy('nom');
        }]);

        $membresDisponibles = Fidele::actifs()
            ->whereNotIn('matricule', $fikambanana->fideles->pluck('matricule'))
            ->orderBy('nom')
            ->get();

        $roles = MembreRole::orderBy('libelle')->get();

        return view('fikambanana.show', compact('fikambanana', 'membresDisponibles', 'roles'));
    }

    public function edit(Fikambanana $fikambanana)
    {
        $this->authorize_role('modif');
        return view('fikambanana.edit', compact('fikambanana'));
    }

    public function update(Request $request, Fikambanana $fikambanana)
    {
        $this->authorize_role('modif');

        $validated = $request->validate([
            'libelle_fikambanana' => 'required|string|max:200',
            'st_patron'           => 'nullable|string|max:100',
        ]);

        $fikambanana->update($validated);

        return redirect()->route('fikambananas.show', $fikambanana->idfikambanana)
            ->with('success', 'Association mise à jour.');
    }

    public function destroy(Fikambanana $fikambanana)
    {
        $this->authorize_role('suppr');
        $nom = $fikambanana->libelle_fikambanana;
        $fikambanana->delete();

        return redirect()->route('fikambananas.index')
            ->with('success', "« {$nom} » supprimé.");
    }

    // ------------------------------------------------------------------
    // Ajouter un membre à l'association
    // ------------------------------------------------------------------
    public function ajouterMembre(Request $request, Fikambanana $fikambanana)
    {
        $this->authorize_role('ajout');

        $validated = $request->validate([
            'matricule'     => 'required|exists:fidele,matricule',
            'date_adhesion' => 'nullable|date',
            'code'          => 'nullable|exists:membre_role,code',
        ]);

        $fikambanana->fideles()->attach($validated['matricule'], [
            'date_adhesion' => $validated['date_adhesion'] ?? now(),
            'code'          => $validated['code'] ?? null,
        ]);

        return back()->with('success', 'Membre ajouté à l\'association.');
    }

    // ------------------------------------------------------------------
    // Retirer un membre de l'association
    // ------------------------------------------------------------------
    public function retirerMembre(Fikambanana $fikambanana, string $matricule)
    {
        $this->authorize_role('suppr');
        $fikambanana->fideles()->detach($matricule);

        return back()->with('success', 'Membre retiré de l\'association.');
    }

    private function authorize_role(string $role): void
    {
        $niveaux = ['lecture' => 0, 'modif' => 1, 'ajout' => 2, 'suppr' => 3, 'admin' => 4];
        if (($niveaux[auth()->user()->role] ?? -1) < ($niveaux[$role] ?? 99)) {
            abort(403);
        }
    }
}
