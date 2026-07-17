{{-- ============================================================ --}}
{{-- FICHIER : resources/views/finances/recap/par_rubrique.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Récapitulatif par rubrique')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-list-columns-reverse text-primary me-2"></i>Récapitulatif par rubrique</h1>
            @if($journal)
                <small class="text-muted">
                    {{ $moisListe->firstWhere('numero', $mois)?->libelle_mois_fr }} {{ $annee }}
                </small>
            @endif
            <a href="{{ route('finances.recap.rubrique.pdf', ['mois' => $mois, 'annee' => $annee]) }}"
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

    @if(!$journal)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Aucun journal pour cette période.
        </div>
    @else

    {{-- Totaux --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted">Total Recettes</div>
                        <div class="fs-5 fw-bold text-success font-monospace">
                            {{ number_format($totalRecettes, 0, ',', ' ') }} Ar
                        </div>
                    </div>
                    <i class="bi bi-arrow-down-circle-fill text-success fs-3 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted">Total Dépenses</div>
                        <div class="fs-5 fw-bold text-danger font-monospace">
                            {{ number_format($totalDepenses, 0, ',', ' ') }} Ar
                        </div>
                    </div>
                    <i class="bi bi-arrow-up-circle-fill text-danger fs-3 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @php $solde = $totalRecettes - $totalDepenses; @endphp
            <div class="card border-{{ $solde >= 0 ? 'success' : 'danger' }} shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted">Résultat</div>
                        <div class="fs-5 fw-bold font-monospace text-{{ $solde >= 0 ? 'success' : 'danger' }}">
                            {{ ($solde >= 0 ? '+' : '') . number_format($solde, 0, ',', ' ') }} Ar
                        </div>
                    </div>
                    <i class="bi bi-graph-{{ $solde >= 0 ? 'up' : 'down' }}-arrow fs-3 opacity-25
                       text-{{ $solde >= 0 ? 'success' : 'danger' }}"></i>
                </div>
            </div>
        </div>
    </div>
{{-- Tableau par rubrique groupé par chapitre --}}


    @foreach($rubriques as $chapCode => $rubs)

        @php
            $estRecette = str_starts_with($chapCode, 'A');
            $chapLibelle = $rubs->first()->chap_libelle ?? $chapCode;
            $totalChap  = $rubs->sum(fn($r) => $donnees[$r->rubrique_id]->total ?? 0);
        @endphp

        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold
                {{ $estRecette ? 'bg-success text-white' : 'bg-danger text-white' }}
                d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-{{ $estRecette ? 'arrow-down-circle' : 'arrow-up-circle' }} me-2"></i>
                    {{ $chapCode }} — {{ $chapLibelle }}
                </span>
                <span class="font-monospace">{{ number_format($totalChap, 0, ',', ' ') }} Ar</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0" style="table-layout:fixed;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:100px;">Code</th>
                            <th>Libellé</th>
                            <th style="width:180px;" class="text-end">Montant (Ar)</th>
                            <th style="width:100px;" class="text-end">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rubs as $r)
                            @php
                                $total    = $donnees[$r->rubrique_id]->total ?? 0;
                                $totalRef = $estRecette ? $totalRecettes : $totalDepenses;
                                $pct      = $totalRef > 0 ? round($total / $totalRef * 100, 1) : 0;
                            @endphp
                            <tr {{ $total == 0 ? 'class=text-muted' : '' }}>
                                <td><code>{{ $r->rubrique_id }}</code></td>
                                <td class="text-truncate" title="{{ $r->rubrique_libelle }}">
                                    {{ $r->rubrique_libelle }}
                                </td>
                                <td class="text-end font-monospace fw-semibold
                                    {{ $total > 0 ? ($estRecette ? 'text-success' : 'text-danger') : 'text-muted' }}">
                                    {{ number_format($total, 0, ',', ' ') }}
                                </td>
                                <td class="text-end">
                                    @if($total > 0)
                                        <div class="d-flex align-items-center gap-1 justify-content-end">
                                            <div class="progress flex-grow-1" style="height:6px; max-width:60px;">
                                                <div class="progress-bar bg-{{ $estRecette ? 'success' : 'danger' }}"
                                                    style="width:{{ $pct }}%"></div>
                                            </div>
                                            <small>{{ $pct }}%</small>
                                        </div>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Sous-total</td>
                            <td class="text-end font-monospace
                                {{ $estRecette ? 'text-success' : 'text-danger' }}">
                                {{ number_format($totalChap, 0, ',', ' ') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endforeach

    @endif

</div>
@endsection
