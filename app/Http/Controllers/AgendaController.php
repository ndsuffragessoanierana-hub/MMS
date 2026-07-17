<?php
// =============================================================================
// FICHIER : app/Http/Controllers/AgendaController.php
// =============================================================================

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $dateDebut = $request->get('date_debut', now()->startOfMonth()->format('Y-m-d'));
        $dateFin   = $request->get('date_fin',   now()->endOfMonth()->format('Y-m-d'));

        $evenements = Agenda::whereBetween('date_agenda', [$dateDebut, $dateFin])
            ->orderBy('date_agenda')
            ->get();

        return view('agenda.index', compact('evenements', 'dateDebut', 'dateFin'));
    }

    public function create()
    {
        $this->authorize_role('ajout');
        return view('agenda.create');
    }

    public function store(Request $request)
    {
        $this->authorize_role('ajout');

        $validated = $request->validate([
            'date_agenda' => 'required|date',
            'libelle'     => 'required|string|max:500',
            'observation' => 'nullable|string',
        ]);

        Agenda::create($validated);

        return redirect()->route('agenda.index')
            ->with('success', 'Événement ajouté à l\'agenda.');
    }

    public function show(Agenda $agenda)
    {
        return view('agenda.show', compact('agenda'));
    }

    public function edit(Agenda $agenda)
    {
        $this->authorize_role('modif');
        return view('agenda.edit', compact('agenda'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $this->authorize_role('modif');

        $validated = $request->validate([
            'date_agenda' => 'required|date',
            'libelle'     => 'required|string|max:500',
            'observation' => 'nullable|string',
        ]);

        $agenda->update($validated);

        return redirect()->route('agenda.index')
            ->with('success', 'Événement mis à jour.');
    }

    public function destroy(Agenda $agenda)
    {
        $this->authorize_role('suppr');
        $agenda->delete();

        return redirect()->route('agenda.index')
            ->with('success', 'Événement supprimé.');
    }

    private function authorize_role(string $role): void
    {
        $niveaux = ['lecture' => 0, 'modif' => 1, 'ajout' => 2, 'suppr' => 3, 'admin' => 4];
        if (($niveaux[auth()->user()->role] ?? -1) < ($niveaux[$role] ?? 99)) {
            abort(403);
        }
    }
}
