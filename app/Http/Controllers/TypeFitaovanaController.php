<?php
// =============================================================================
// FICHIER : app/Http/Controllers/TypeFitaovanaController.php
// =============================================================================


namespace App\Http\Controllers;

use App\Models\TypeFitaovana;
use Illuminate\Http\Request;

class TypeFitaovanaController extends Controller
{
    public function index()
    {
        $types = TypeFitaovana::withCount('fitaovanas')->orderBy('libelle_type_fitaovana')->get();
        return view('type-fitaovana.index', compact('types'));
    }

    public function create()
    {
        $this->authorize_role('ajout');
        return view('type-fitaovana.create');
    }

    public function store(Request $request)
    {
        $this->authorize_role('ajout');

        $validated = $request->validate([
            'id_type_fitaovana'      => 'required|string|max:5|unique:type_fitaovana,id_type_fitaovana',
            'libelle_type_fitaovana' => 'required|string|max:200',
        ]);

        TypeFitaovana::create($validated);

        return redirect()->route('type-fitaovanas.index')
            ->with('success', 'Type d\'équipement créé.');
    }

    public function edit(TypeFitaovana $type_fitaovana)  // ← snake_case
    {
        $this->authorize_role('modif');
        return view('type-fitaovana.edit', ['typeFitaovana' => $type_fitaovana]); // ← correction
    }

    public function update(Request $request, TypeFitaovana $type_fitaovana)
    {
        $this->authorize_role('modif');

        $ancienCode = $type_fitaovana->id_type_fitaovana;

        $validated = $request->validate([
            'id_type_fitaovana'      => 'required|string|max:5|unique:type_fitaovana,id_type_fitaovana,' . $ancienCode . ',id_type_fitaovana',
            'libelle_type_fitaovana' => 'required|string|max:200',
        ]);

        // Si le code a changé → delete + recreate (PK non modifiable directement)
        if ($validated['id_type_fitaovana'] !== $ancienCode) {

            // Vérifier qu'aucun équipement n'utilise l'ancien code avant de recréer
            if ($type_fitaovana->fitaovanas()->exists()) {
                return back()
                    ->withInput()
                    ->with('error', 'Impossible de modifier le code : des équipements utilisent ce type.');
            }

            $type_fitaovana->delete();
            TypeFitaovana::create($validated);

        } else {
            // Code inchangé → simple update du libellé
            $type_fitaovana->update([
                'libelle_type_fitaovana' => $validated['libelle_type_fitaovana'],
            ]);
        }

        return redirect()->route('type-fitaovanas.index')
            ->with('success', 'Type mis à jour.');
    }

    public function destroy(TypeFitaovana $type_fitaovana)  // ← snake_case
    {
        $this->authorize_role('suppr');

        if ($type_fitaovana->fitaovanas()->exists()) {
            return back()->with('error', 'Impossible : des équipements utilisent ce type.');
        }

        $type_fitaovana->delete();

        return redirect()->route('type-fitaovanas.index')
            ->with('success', 'Type supprimé.');
    }

    private function authorize_role(string $role): void
    {
        $niveaux = ['lecture' => 0, 'modif' => 1, 'ajout' => 2, 'suppr' => 3, 'admin' => 4];
        if (($niveaux[auth()->user()->role] ?? -1) < ($niveaux[$role] ?? 99)) {
            abort(403);
        }
    }
}