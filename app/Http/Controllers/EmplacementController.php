<?php

namespace App\Http\Controllers;

use App\Models\Emplacement;
use App\Models\Fitaovana;
use App\Services\EquipmentLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmplacementController extends Controller
{
    public function index(Request $request)
    {
        $query = Emplacement::withCount('fitaovanas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('libelle_place', 'ilike', "%{$search}%")
                  ->orWhere('toerana', 'ilike', "%{$search}%")
                  ->orWhere('nom_court', 'ilike', "%{$search}%")
                  ->orWhere('idplace', 'ilike', "%{$search}%");
            });
        }

        $emplacements = $query->orderBy('libelle_place')->paginate(15)->withQueryString();

        return view('emplacements.index', compact('emplacements'));
    }

    public function create()
    {
        return view('emplacements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idplace'      => ['required', 'string', 'max:50', 'unique:emplacement,idplace'],
            'libelle_place' => ['required', 'string', 'max:255'],
            'toerana'      => ['nullable', 'string', 'max:255'],
            'nom_court'    => ['nullable', 'string', 'max:50'],
        ]);

        // updateOrInsert par cohérence avec le reste du projet (clé non auto-incrémentée)
        DB::table('emplacement')->updateOrInsert(
            ['idplace' => $validated['idplace']],
            [
                'libelle_place' => $validated['libelle_place'],
                'toerana'       => $validated['toerana'] ?? null,
                'nom_court'     => $validated['nom_court'] ?? null,
            ]
        );

        return redirect()
            ->route('emplacements.index')
            ->with('success', "Emplacement « {$validated['libelle_place']} » créé avec succès.");
    }

    public function show(Emplacement $emplacement)
    {
        $emplacement->load('fitaovanas');

        // Équipements pas encore présents à cet emplacement (peuvent être ailleurs ou nulle part)
        $availableFitaovanas = Fitaovana::whereNotIn('idfitaovana',
                $emplacement->fitaovanas->pluck('idfitaovana')
            )
            ->orderBy('denomination')
            ->get();

        // Pour affichage "actuellement à ..." dans le select, on résout l'emplacement courant en une seule requête
        $currentLocations = DB::table('empla_fitaovana')
            ->join('emplacement', 'emplacement.idplace', '=', 'empla_fitaovana.emp_idplace')
            ->pluck('emplacement.libelle_place', 'empla_fitaovana.fit_idfitaovana');

        $availableFitaovanas->each(function ($fitaovana) use ($currentLocations) {
            $fitaovana->currentEmplacementLabel = $currentLocations->get($fitaovana->idfitaovana);
        });

        return view('emplacements.show', compact('emplacement', 'availableFitaovanas'));
    }

    /**
     * Assigne / déplace un équipement vers cet emplacement.
     * Retire automatiquement l'ancienne affectation (un seul emplacement à la fois).
     */
    public function addEquipment(Request $request, Emplacement $emplacement, EquipmentLocationService $service)
    {
        $validated = $request->validate([
            'fit_idfitaovana' => ['required', 'integer', 'exists:fitaovana,idfitaovana'],
        ]);

        $service->moveTo($validated['fit_idfitaovana'], $emplacement->idplace);

        return back()->with('success', 'Équipement déplacé vers cet emplacement.');
    }

    /**
     * Retire un équipement de cet emplacement (il n'est plus rattaché à aucun lieu).
     */
    public function removeEquipment(Emplacement $emplacement, int $fitaovana, EquipmentLocationService $service)
    {
        $service->remove($fitaovana, $emplacement->idplace);

        return back()->with('success', 'Équipement retiré de cet emplacement.');
    }

    public function edit(Emplacement $emplacement)
    {
        return view('emplacements.edit', compact('emplacement'));
    }

    public function update(Request $request, Emplacement $emplacement)
    {
        $validated = $request->validate([
            'idplace' => [
                'required', 'string', 'max:50',
                Rule::unique('emplacement', 'idplace')->ignore($emplacement->idplace, 'idplace'),
            ],
            'libelle_place' => ['required', 'string', 'max:255'],
            'toerana'       => ['nullable', 'string', 'max:255'],
            'nom_court'     => ['nullable', 'string', 'max:50'],
        ]);

        DB::table('emplacement')
            ->where('idplace', $emplacement->idplace)
            ->update([
                'idplace'       => $validated['idplace'],
                'libelle_place' => $validated['libelle_place'],
                'toerana'       => $validated['toerana'] ?? null,
                'nom_court'     => $validated['nom_court'] ?? null,
            ]);

        return redirect()
            ->route('emplacements.index')
            ->with('success', "Emplacement « {$validated['libelle_place']} » mis à jour.");
    }

    public function destroy(Emplacement $emplacement)
    {
        if ($emplacement->fitaovanas()->count() > 0) {
            return redirect()
                ->route('emplacements.index')
                ->with('error', "Impossible de supprimer « {$emplacement->libelle_place} » : des équipements y sont encore rattachés.");
        }

        DB::table('emplacement')->where('idplace', $emplacement->idplace)->delete();

        return redirect()
            ->route('emplacements.index')
            ->with('success', 'Emplacement supprimé.');
    }
}
