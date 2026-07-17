<?php
// =============================================================================
// ECAR — Module Finances : CRUD complet
// Livre Journal · Rubriques · Chapitres · Comptes · Budget · Récapitulatif
// =============================================================================
// Fichiers à créer :
//   app/Http/Controllers/Finance/JournalController.php
//   app/Http/Controllers/Finance/RubriqueController.php
//   app/Http/Controllers/Finance/ChapitreController.php
//   app/Http/Controllers/Finance/CompteController.php
//   app/Http/Controllers/Finance/BudgetController.php
//   app/Http/Controllers/Finance/RecapController.php
//   app/Http/Requests/StoreJournalRequest.php
//   app/Http/Requests/StoreDetailJournalRequest.php
//   app/Http/Requests/StoreRubriqueRequest.php
//   routes/web.php (extrait finances)
//   resources/views/finances/journal/index.blade.php
//   resources/views/finances/journal/show.blade.php
//   resources/views/finances/journal/_form_ecriture.blade.php
//   resources/views/finances/rubriques/index.blade.php
//   resources/views/finances/recap/index.blade.php
//   resources/views/finances/dashboard.blade.php
// =============================================================================


// =============================================================================
// FICHIER : resources/views/finances/journal/index.blade.php
// =============================================================================
?>
{{-- ========== finances/journal/index.blade.php ========== --}}
@extends('layouts.app')
@section('title', 'Livre Journal')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-success"><i class="bi bi-journal-text me-2"></i>Livre Journal</h1>
            <small class="text-muted">Exercice {{ $annee }}</small>
        </div>
        @if(auth()->user()->peutAjouter())
        <a href="{{ route('finances.journals.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i>Ouvrir un mois
        </a>
        @endif
    </div>

    {{-- Sélecteur d'année --}}
    <form method="GET" class="d-flex gap-2 mb-4 align-items-center">
        <label class="fw-semibold small">Année :</label>
        <select name="annee" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
            @foreach($annees as $a)
                <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
    </form>

    {{-- Tableau des journaux mensuels --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mois</th>
                        <th class="text-end">Solde BNI</th>
                        <th class="text-end">Solde BFV</th>
                        <th class="text-end">Solde Caisse</th>
                        <th class="text-end fw-bold">Total Soldes</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($journals as $j)
                    <tr>
                        <td class="fw-semibold">{{ $j->periode }}</td>
                        <td class="text-end font-monospace">{{ number_format($j->journal_solde_bni,   0, ',', ' ') }}</td>
                        <td class="text-end font-monospace">{{ number_format($j->journal_solde_bfv,   0, ',', ' ') }}</td>
                        <td class="text-end font-monospace">{{ number_format($j->journal_solde_caisse,0, ',', ' ') }}</td>
                        <td class="text-end font-monospace fw-bold text-success">
                            {{ number_format($j->solde_total, 0, ',', ' ') }} Ar
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('finances.journals.show', $j->journal_id) }}"
                                   class="btn btn-outline-success" title="Voir les écritures">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->peutSupprimer())
                                <form action="{{ route('finances.journals.destroy', $j->journal_id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Supprimer ce journal et toutes ses écritures ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                            Aucun journal pour {{ $annee }}.
                            @if(auth()->user()->peutAjouter())
                                <a href="{{ route('finances.journals.create') }}">Ouvrir le premier mois</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($journals->count())
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td>TOTAL {{ $annee }}</td>
                        <td class="text-end font-monospace">{{ number_format($totaux->total_bni   ?? 0, 0, ',', ' ') }}</td>
                        <td class="text-end font-monospace">{{ number_format($totaux->total_bfv   ?? 0, 0, ',', ' ') }}</td>
                        <td class="text-end font-monospace">{{ number_format($totaux->total_caisse?? 0, 0, ',', ' ') }}</td>
                        <td class="text-end font-monospace text-success">
                            {{ number_format(($totaux->total_bni ?? 0) + ($totaux->total_bfv ?? 0) + ($totaux->total_caisse ?? 0), 0, ',', ' ') }} Ar
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Liens rapides --}}
    <div class="row g-3 mt-3">
        <div class="col-md-3">
            <a href="{{ route('finances.recap.rubrique') }}" class="card text-decoration-none h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center py-3">
                    <i class="bi bi-list-columns-reverse fs-3 text-primary"></i>
                    <div class="small fw-semibold mt-1">Récap. par rubrique</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('finances.recap.chapitre') }}" class="card text-decoration-none h-100 shadow-sm border-0">
                <div class="card-body text-center py-3">
                    <i class="bi bi-bar-chart fs-3 text-warning"></i>
                    <div class="small fw-semibold mt-1">Récap. par chapitre</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('finances.recap.evolution') }}" class="card text-decoration-none h-100 shadow-sm border-0">
                <div class="card-body text-center py-3">
                    <i class="bi bi-graph-up-arrow fs-3 text-success"></i>
                    <div class="small fw-semibold mt-1">Évolution solde</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('finances.budget.annuel') }}" class="card text-decoration-none h-100 shadow-sm border-0">
                <div class="card-body text-center py-3">
                    <i class="bi bi-clipboard2-data fs-3 text-info"></i>
                    <div class="small fw-semibold mt-1">Budget prévisionnel</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
