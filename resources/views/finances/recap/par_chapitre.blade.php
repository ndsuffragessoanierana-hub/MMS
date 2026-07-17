{{-- ============================================================ --}}
{{-- FICHIER : resources/views/finances/recap/par_chapitre.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Récapitulatif par chapitre')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-pie-chart-fill text-warning me-2"></i>Récapitulatif par chapitre</h1>
            <small class="text-muted">
                {{ $moisListe->firstWhere('numero', $mois)?->libelle_mois_fr }} {{ $annee }}
            </small>

            <a href="{{ route('finances.recap.chapitre.pdf', ['mois' => $mois, 'annee' => $annee]) }}"
                class="btn btn-outline-danger btn-sm" target="_blank">
                <i class="bi bi-file-pdf me-1"></i>PDF
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Mois</label>
                    <select name="mois" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($moisListe as $m)
                            <option value="{{ $m->numero }}" {{ $m->numero == $mois ? 'selected' : '' }}>
                                {{ $m->libelle_mois_fr }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Année</label>
                    <select name="annee" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($annees as $a)
                            <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($donnees->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Aucune donnée pour cette période.
        </div>
    @else

    {{-- Tableau par chapitre --}}
    @php
        $totalRecettes = $donnees->filter(fn($d) => str_starts_with($d->chap_code, 'A'))->sum('total');
        $totalDepenses = $donnees->filter(fn($d) => str_starts_with($d->chap_code, 'B'))->sum('total');
        $solde         = $totalRecettes - $totalDepenses;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <div class="small text-muted">Total Recettes</div>
                    <div class="fs-5 fw-bold text-success font-monospace">
                        {{ number_format($totalRecettes, 0, ',', ' ') }} Ar
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-body text-center">
                    <div class="small text-muted">Total Dépenses</div>
                    <div class="fs-5 fw-bold text-danger font-monospace">
                        {{ number_format($totalDepenses, 0, ',', ' ') }} Ar
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-{{ $solde >= 0 ? 'success' : 'danger' }} shadow-sm">
                <div class="card-body text-center">
                    <div class="small text-muted">Résultat</div>
                    <div class="fs-5 fw-bold font-monospace text-{{ $solde >= 0 ? 'success' : 'danger' }}">
                        {{ ($solde >= 0 ? '+' : '') . number_format($solde, 0, ',', ' ') }} Ar
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:120px;">Code</th>
                        <th>Chapitre</th>
                        <th style="width:100px;" class="text-center">Type</th>
                        <th style="width:200px;" class="text-end">Montant (Ar)</th>
                        <th style="width:150px;" class="text-end">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donnees as $d)
                        @php
                            $estRecette = str_starts_with($d->chap_code, 'A');
                            $totalRef   = $estRecette ? $totalRecettes : $totalDepenses;
                            $pct        = $totalRef > 0 ? round($d->total / $totalRef * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td><code class="fw-bold">{{ $d->chap_code }}</code></td>
                            <td class="fw-semibold">{{ $d->chap_libelle }}</td>
                            <td class="text-center">
                                @if($estRecette)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-arrow-down-circle me-1"></i>Recette
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        <i class="bi bi-arrow-up-circle me-1"></i>Dépense
                                    </span>
                                @endif
                            </td>
                            <td class="text-end font-monospace fw-bold
                                {{ $estRecette ? 'text-success' : 'text-danger' }}">
                                {{ number_format($d->total, 0, ',', ' ') }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center gap-2 justify-content-end">
                                    <div class="progress flex-grow-1" style="height:8px; max-width:80px;">
                                        <div class="progress-bar bg-{{ $estRecette ? 'success' : 'danger' }}"
                                             style="width:{{ $pct }}%"></div>
                                    </div>
                                    <small class="fw-semibold">{{ $pct }}%</small>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">TOTAL</td>
                        <td class="text-end font-monospace">
                            <div class="text-success">↓ {{ number_format($totalRecettes, 0, ',', ' ') }}</div>
                            <div class="text-danger">↑ {{ number_format($totalDepenses, 0, ',', ' ') }}</div>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @endif

</div>
@endsection
