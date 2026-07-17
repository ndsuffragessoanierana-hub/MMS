{{-- ============================================================ --}}
{{-- FICHIER : resources/views/finances/budget/mensuel.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Budget prévisionnel mensuel')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-calendar3 text-info me-2"></i>Budget prévisionnel mensuel</h1>
            @if($exercice)
                <small class="text-muted">
                    {{ $exercice->date_debut?->format('d/m/Y') }}
                    →
                    {{ $exercice->date_fin?->format('d/m/Y') }}
                </small>
            @endif
        </div>
    </div>

    {{-- Sélecteur d'exercice --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2 align-items-end">
                <div>
                    <label class="form-label small fw-semibold">Exercice</label>
                    <select name="exercice_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($exercices as $ex)
                            <option value="{{ $ex->id_exercice }}"
                                {{ $ex->id_exercice == $exercice?->id_exercice ? 'selected' : '' }}>
                                {{ $ex->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if(!$exercice)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Aucun exercice trouvé.
        </div>
    @else

    <form action="{{ route('finances.budget.mensuel.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_exercice" value="{{ $exercice->id_exercice }}">

        @foreach($rubriques->groupBy('chap_code') as $chapCode => $rubs)
            @php
                $chapitre   = $rubs->first()->chapitre;
                $estRecette = str_starts_with($chapCode, 'A');
            @endphp

            <div class="card shadow-sm mb-3">
                <div class="card-header fw-semibold
                    {{ $estRecette ? 'bg-success text-white' : 'bg-danger text-white' }}">
                    <i class="bi bi-{{ $estRecette ? 'arrow-down-circle' : 'arrow-up-circle' }} me-2"></i>
                    {{ $chapCode }} — {{ $chapitre?->chap_libelle }}
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0" style="font-size:.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:180px;">Rubrique</th>
                                    @foreach($moisListe as $m)
                                        <th class="text-center" style="min-width:80px;">
                                            {{ substr($m->libelle_mois_fr, 0, 3) }}
                                        </th>
                                    @endforeach
                                    <th class="text-center bg-light">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rubs as $r)
                                    @php
                                        $budgetsMois = $r->ligneBudgetMensuels->keyBy('mois');
                                        $totalRubrique = 0;
                                    @endphp
                                    <tr>
                                        <td class="small fw-semibold">
                                            <code class="me-1">{{ $r->rubrique_id }}</code>
                                            {{ Str::limit($r->rubrique_libelle, 25) }}
                                        </td>
                                        @foreach($moisListe as $m)
                                            @php
                                                $montantMois = $budgetsMois[$m->numero]?->lg_bdg_montant ?? 0;
                                                $totalRubrique += $montantMois;
                                            @endphp
                                            <td>
                                                <input type="number"
                                                       name="lignes[{{ $r->rubrique_id }}][{{ $m->numero }}]"
                                                       value="{{ old('lignes.'.$r->rubrique_id.'.'.$m->numero, $montantMois ?: '') }}"
                                                       class="form-control form-control-sm text-end p-1 montant-input"
                                                       data-rubrique="{{ $r->rubrique_id }}"
                                                       data-mois="{{ $m->numero }}"
                                                       min="0" step="1" placeholder="0"
                                                       style="min-width:75px;">
                                            </td>
                                        @endforeach
                                        <td class="text-end fw-bold font-monospace bg-light total-rubrique-{{ Str::slug($r->rubrique_id) }}">
                                            {{ number_format($totalRubrique, 0, ',', ' ') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        @if(auth()->user()->peutModifier())
        <div class="d-flex justify-content-end mt-2">
            <button type="submit" class="btn btn-info text-white">
                <i class="bi bi-check-lg me-1"></i>Enregistrer le budget mensuel
            </button>
        </div>
        @endif

    </form>
    @endif

</div>

@push('scripts')
<script>
// Calcul automatique du total par rubrique à la saisie
document.querySelectorAll('.montant-input').forEach(input => {
    input.addEventListener('input', function() {
        const rubrique = this.dataset.rubrique;
        const slug     = rubrique.toLowerCase().replace(/[^a-z0-9]/g, '-');
        const inputs   = document.querySelectorAll(`input[data-rubrique="${rubrique}"]`);
        let total      = 0;
        inputs.forEach(i => total += parseFloat(i.value) || 0);
        const cell = document.querySelector(`.total-rubrique-${slug}`);
        if (cell) cell.textContent = new Intl.NumberFormat('fr').format(total);
    });
});
</script>
@endpush
@endsection
