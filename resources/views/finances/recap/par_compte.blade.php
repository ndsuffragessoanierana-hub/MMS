@extends('layouts.app')
@section('title', 'Journal par compte')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-bank text-primary me-2"></i>Journal par compte</h1>
            <small class="text-muted">
                {{ \Carbon\Carbon::parse($dateDebut)->locale('fr')->translatedFormat('d F Y') }}
                →
                {{ \Carbon\Carbon::parse($dateFin)->locale('fr')->translatedFormat('d F Y') }}
            </small>
            <a href="{{ route('finances.recap.compte.pdf', ['compte' => $compteId, 'date_debut' => $dateDebut, 'date_fin' => $dateFin]) }}"
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
                    <label class="form-label small fw-semibold">Compte</label>
                    <select name="compte" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">— Tous les comptes —</option>
                        @foreach($comptes as $c)
                            <option value="{{ $c->no_compte }}"
                                {{ $compteId == $c->no_compte ? 'selected' : '' }}>
                                {{ $c->no_compte }} — {{ $c->libelle_compte }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Date début</label>
                    <input type="date" name="date_debut" value="{{ $dateDebut }}"
                        class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Date fin</label>
                    <input type="date" name="date_fin" value="{{ $dateFin }}"
                        class="form-control form-control-sm">
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('finances.recap.compte') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Statistiques --}}
    @if($details->count())
    @php
        $totalRecettes = $totalRecettesGlobal;
        $totalDepenses = $totalDepensesGlobal;
        $solde         = $totalRecettes - $totalDepenses;
    @endphp
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
                    <i class="bi bi-arrow-down-circle-fill text-success fs-2 opacity-25"></i>
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
                    <i class="bi bi-arrow-up-circle-fill text-danger fs-2 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-{{ $solde >= 0 ? 'success' : 'danger' }} shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted">Solde</div>
                        <div class="fs-5 fw-bold font-monospace text-{{ $solde >= 0 ? 'success' : 'danger' }}">
                            {{ ($solde >= 0 ? '+' : '') . number_format($solde, 0, ',', ' ') }} Ar
                        </div>
                    </div>
                    <i class="bi bi-graph-{{ $solde >= 0 ? 'up' : 'down' }}-arrow fs-2 opacity-25
                    text-{{ $solde >= 0 ? 'success' : 'danger' }}"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Table des écritures --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="table-layout:fixed;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:90px;">Date</th>
                            <th style="width:80px;">Mois</th>
                            <th>Libellé</th>
                            <th style="width:100px;">Rubrique</th>
                            <th style="width:100px;">Chapitre</th>
                            <th style="width:110px;">Mode paiement</th>
                            <th style="width:130px;">Compte</th>
                            <th style="width:140px;" class="text-end">Montant (Ar)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $d)
                        <tr>
                            <td class="small">{{ \Carbon\Carbon::parse($d->j_detail_date)->format('d/m/Y') }}</td>
                            <td class="small text-muted">
                                {{ \App\Models\Mois::find($d->journal_mois)?->libelle_mois_fr ?? '—' }}
                            </td>
                            <td class="small text-truncate" title="{{ $d->j_detail_libelle }}">
                                {{ $d->j_detail_libelle }}
                            </td>
                            <td class="small"><code>{{ $d->rub_rubrique_id }}</code></td>
                            <td class="small text-muted">{{ $d->chap_code ?? '—' }}</td>
                            <td class="small">
                                @if($d->j_detail_mode_paie)
                                    @php
                                        $modes = ['ESP'=>'ESPECE','BFV'=>'BRED','BNI'=>'BNI','VIR'=>'VIREMENT','MOB'=>'MOBILE'];
                                        $colors = ['ESP'=>'secondary','BFV'=>'danger','BNI'=>'primary','VIR'=>'info','MOB'=>'success'];
                                        $m = $d->j_detail_mode_paie;
                                    @endphp
                                    <span class="badge bg-{{ $colors[$m] ?? 'secondary' }}-subtle
                                                      text-{{ $colors[$m] ?? 'secondary' }}
                                                      border border-{{ $colors[$m] ?? 'secondary' }}-subtle">
                                        {{ $modes[$m] ?? $m }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="small text-muted text-truncate" title="{{ $d->libelle_compte ?? '' }}">
                                {{ $d->libelle_compte ?? '—' }}
                            </td>
                            <td class="text-end font-monospace fw-semibold
                                text-{{ str_starts_with($d->chap_code ?? '', 'A') ? 'success' : 'danger' }}">
                                {{ number_format($d->j_detail_montant, 0, ',', ' ') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-bank fs-2 d-block mb-2"></i>
                                Aucune écriture trouvée.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($details->count())
                     
                    <tfoot class="table-light fw-bold">
                        <tr>
                           {{-- <td colspan="7" class="text-end">Total page</td>
                            <td class="text-end font-monospace">
                                {{ number_format($details->sum('j_detail_montant'), 0, ',', ' ') }} Ar
                            </td>
                            --}}
                        </tr>
                    </tfoot>
                    
                    @endif
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $details->total() }} écriture(s)</small>
            {{ $details->links() }}
        </div>
    </div>

</div>
@endsection
