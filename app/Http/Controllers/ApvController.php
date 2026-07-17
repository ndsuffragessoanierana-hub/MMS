<?php

namespace App\Http\Controllers;

use App\Models\{Apv, Faritra};
use Illuminate\Http\Request;

class ApvController extends Controller
{
    public function index(Request $request)
    {
        $query = Apv::with('faritra')->orderBy('libelle_apv');

        if ($request->filled('faritra_id')) {
            $query->where('idfaritra', $request->faritra_id);
        }

        $apvs     = $query->withCount('fideles')->get();
        $faritras = Faritra::ordonne()->get();

        return view('apv.index', compact('apvs', 'faritras'));
    }

    public function create()
    {
        $this->authorize_role('ajout');
        $faritras = Faritra::ordonne()->get();
        return view('apv.create', compact('faritras'));
    }

    public function store(Request $request)
    {
        $this->authorize_role('ajout');

        $validated = $request->validate([
            'idapv'       => 'required|string|max:20|unique:apv,idapv',   // ← AJOUT
            'libelle_apv' => 'required|string|max:100',
            'idfaritra'   => 'required|exists:faritra,idfaritra',
        ]);

        $apv = Apv::create($validated);

        return redirect()->route('apvs.index')
            ->with('success', "APV « {$apv->libelle_apv} » créé avec succès.");
    }

    public function show(Apv $apv)
    {
        $apv->load('faritra');
        $apv->loadCount('fideles');
        return view('apv.show', compact('apv'));
    }

    public function edit(Apv $apv)
    {
        $this->authorize_role('modif');
        $faritras = Faritra::ordonne()->get();
        return view('apv.edit', compact('apv', 'faritras'));
    }

    public function update(Request $request, Apv $apv)
    {
        $this->authorize_role('modif');

        $validated = $request->validate([
            'idapv'       => 'required|string|max:20|unique:apv,idapv,' . $apv->idapv . ',idapv',   // ← AJOUT (ignore l'APV en cours)
            'libelle_apv' => 'required|string|max:100',
            'idfaritra'   => 'required|exists:faritra,idfaritra',
        ]);

        $apv->update($validated);

        return redirect()->route('apvs.index')
            ->with('success', 'APV mis à jour.');
    }

    public function destroy(Apv $apv)
    {
        $this->authorize_role('suppr');

        if ($apv->fideles()->exists()) {
            return back()->with('error', 'Impossible : des fidèles sont rattachés à cet APV.');
        }

        $nom = $apv->libelle_apv;
        $apv->delete();

        return redirect()->route('apvs.index')
            ->with('success', "APV « {$nom} » supprimé.");
    }

    private function authorize_role(string $role): void
    {
        $niveaux = ['lecture' => 0, 'modif' => 1, 'ajout' => 2, 'suppr' => 3, 'admin' => 4];
        if (($niveaux[auth()->user()->role] ?? -1) < ($niveaux[$role] ?? 99)) {
            abort(403);
        }
    }
}