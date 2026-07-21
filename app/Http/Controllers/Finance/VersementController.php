<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VersementController extends Controller
{
    // Liste des lignes de versement pour une écriture donnée
    public function index($j_detail_numero)
    {
        $lignes = DB::table('t_detail_versement')
            ->where('j_detail_numero', $j_detail_numero)
            ->orderBy('id')
            ->get();

        return response()->json($lignes);
    }

    // Ajouter une ligne
    public function store(Request $request, $j_detail_numero)
    {
        $request->validate([
            'libelle'  => 'required|string|max:100',
            'montant'  => 'required|numeric|min:0',
            'remarque' => 'nullable|string|max:100',
        ]);

        $nextId = DB::table('t_detail_versement')
            ->where('j_detail_numero', $j_detail_numero)
            ->max('id') + 1;

        DB::table('t_detail_versement')->insert([
            'j_detail_numero' => $j_detail_numero,
            'id'              => $nextId,
            'libelle'         => $request->libelle,
            'montant'         => $request->montant,
            'remarque'        => $request->remarque,
        ]);

        return response()->json(['success' => true, 'message' => 'Ligne ajoutée.']);
    }

    // Modifier une ligne
    public function update(Request $request, $j_detail_numero, $id)
    {
        $request->validate([
            'libelle'  => 'required|string|max:100',
            'montant'  => 'required|numeric|min:0',
            'remarque' => 'nullable|string|max:100',
        ]);

        DB::table('t_detail_versement')
            ->where('j_detail_numero', $j_detail_numero)
            ->where('id', $id)
            ->update([
                'libelle'  => $request->libelle,
                'montant'  => $request->montant,
                'remarque' => $request->remarque,
            ]);

        return response()->json(['success' => true, 'message' => 'Ligne modifiée.']);
    }

    // Supprimer une ligne
    public function destroy($j_detail_numero, $id)
    {
        DB::table('t_detail_versement')
            ->where('j_detail_numero', $j_detail_numero)
            ->where('id', $id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Ligne supprimée.']);
    }
}