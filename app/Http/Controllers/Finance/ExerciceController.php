<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Exercice;
use Illuminate\Http\Request;

class ExerciceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'annee' => 'required|integer|min:2000|max:2100|unique:exercice,id_exercice',
        ]);

        Exercice::create([
            'id_exercice'  => $request->annee,
            'actif'  => $request->has('actif') ? 'O' : 'N',
        ]);

        return back()->with('success', 'Exercice ' . $request->annee . ' créé.');
    }
}