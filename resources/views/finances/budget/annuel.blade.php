{{-- ============================================================ --}}
{{-- FICHIER : resources/views/finances/budget/annuel.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Budget prévisionnel annuel')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-clipboard2-data-fill text-info me-2"></i>Budget prévisionnel annuel</h1>
            
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
                @if(auth()->user()->peutAjouter() && !$exercices->contains('annee', now()->year))
                <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalExercice">
                    <i class="bi bi-plus-circle me-1"></i>Nouvel exercice
                </a>
                @endif
            </form>
        </div>
    </div>

    @if(!$exercice)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Aucun exercice trouvé. Créez d'abord un exercice budgétaire.
        </div>
    @else

    {{-- Formulaire budget --}}
    <form action="{{ route('finances.budget.annuel.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_exercice" value="{{ $exercice->id_exercice }}">

        @php
            $totalRecettes = 0;
            $totalDepenses = 0;
        @endphp

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
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Rubrique</th>
                                <th style="width:200px;" class="text-end">Montant prévu (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rubs as $r)
                                @php
                                    $budget = $r->ligneBudgets->first();
                                    $montant = $budget?->lg_bdg_montant ?? 0;
                                    if ($estRecette) $totalRecettes += $montant;
                                    else             $totalDepenses += $montant;
                                @endphp
                                <tr>
                                    <td><code class="small">{{ $r->rubrique_id }}</code></td>
                                    <td class="small">{{ $r->rubrique_libelle }}</td>
                                    <td class="text-end">
                                        <input type="number"
                                               name="lignes[{{ $loop->parent->index }}_{{ $loop->index }}][rub]"
                                               value="{{ $r->rubrique_id }}"
                                               hidden>
                                        <input type="number"
                                               name="lignes[{{ $loop->parent->index }}_{{ $loop->index }}][montant]"
                                               value="{{ old('lignes.'.$loop->parent->index.'_'.$loop->index.'.montant', $montant) }}"
                                               class="form-control form-control-sm text-end"
                                               style="width:180px; margin-left:auto;"
                                               min="0" step="1" placeholder="0">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        {{-- Totaux --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-md-4">
                        <div class="text-muted small">Total Recettes prévues</div>
                        <div class="fs-5 fw-bold text-success font-monospace">
                            {{ number_format($totalRecettes, 0, ',', ' ') }} Ar
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Total Dépenses prévues</div>
                        <div class="fs-5 fw-bold text-danger font-monospace">
                            {{ number_format($totalDepenses, 0, ',', ' ') }} Ar
                        </div>
                    </div>
                    <div class="col-md-4">
                        @php $solde = $totalRecettes - $totalDepenses; @endphp
                        <div class="text-muted small">Solde prévisionnel</div>
                        <div class="fs-5 fw-bold font-monospace text-{{ $solde >= 0 ? 'success' : 'danger' }}">
                            {{ ($solde >= 0 ? '+' : '') . number_format($solde, 0, ',', ' ') }} Ar
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->peutModifier())
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-info text-white">
                <i class="bi bi-check-lg me-1"></i>Enregistrer le budget
            </button>
        </div>
        @endif

    </form>
    @endif

</div>

{{-- Modal nouvel exercice --}}
<div class="modal fade" id="modalExercice" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            
            <form action="{{ url('finances/exercices') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nouvel exercice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Année</label>
                    <input type="number" name="annee" value="{{ now()->year }}"
                           class="form-control" min="2000" max="2100">
                    <div class="form-check mt-2">
                        <input type="checkbox" name="actif" value="O" class="form-check-input" id="actifCheck" checked>
                        <label class="form-check-label" for="actifCheck">Exercice actif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

