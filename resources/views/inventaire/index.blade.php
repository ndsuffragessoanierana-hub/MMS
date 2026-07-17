<?php
// =============================================================================
// FICHIER : resources/views/inventaire/index.blade.php
// =============================================================================
?>
{{-- ========== inventaire/index.blade.php ========== --}}
@extends('layouts.app')
@section('title', 'Inventaire des équipements')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-warning"><i class="bi bi-box-seam-fill me-2"></i>Inventaire — Fitaovana</h1>
            <small class="text-muted">{{ $stats['total'] }} équipement(s) enregistré(s)</small>
        </div>
        @if(auth()->user()->peutAjouter())
        <a href="{{ route('inventaire.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-circle me-1"></i>Nouvel équipement
        </a>
        @endif
        <a href="{{ route('inventaire.pdf') }}"
            class="btn btn-outline-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Exporter PDF
        </a>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-warning">{{ $stats['total'] }}</div>
                <div class="small text-muted">Équipements</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-info">{{ $stats['nb_types'] }}</div>
                <div class="small text-muted">Types de matériels</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center py-3">
                <div class="fs-5 fw-bold text-success font-monospace">
                    {{ number_format($stats['valeur_totale'], 0, ',', ' ') }} Ar
                </div>
                <div class="small text-muted">Valeur totale inventaire</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="text" name="recherche" value="{{ request('recherche') }}"
                           class="form-control form-control-sm"
                           placeholder="Dénomination, N° inventaire, référence…">
                </div>
                <div class="col-md-3">
                    <select name="type_id" class="form-select form-select-sm">
                        <option value="">— Tous les types —</option>
                        @foreach($types as $t)
                            <option value="{{ $t->id_type_fitaovana }}"
                                {{ request('type_id') == $t->id_type_fitaovana ? 'selected':'' }}>
                                {{ $t->libelle_type_fitaovana }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('inventaire.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Inventaire</th>
                            <th>Dénomination</th>
                            <th>Type</th>
                            <th>Référence</th>
                            <th>Date acq.</th>
                            <th class="text-end">Valeur unit.</th>
                            <th class="text-center">Qté</th>
                            <th class="text-end">Valeur totale</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fitaovanas as $f)
                        <tr>
                            <td><code class="small">{{ $f->no_inventaire ?? '—' }}</code></td>
                            <td>
                                <a href="{{ route('inventaire.show', $f->idfitaovana) }}"
                                   class="fw-semibold text-decoration-none">
                                    {{ $f->denomination }}
                                </a>
                                @if($f->fournisseur)
                                    <br><small class="text-muted">{{ $f->fournisseur }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-warning-subtle text-warning border border-warning-subtle small">
                                {{ $f->typeFitaovana?->libelle_type_fitaovana ?? '—' }}</span>
                            </td>
                            <td class="small text-muted">{{ $f->reference ?? '—' }}</td>
                            <td class="small">{{ $f->date_acquisition?->format('d/m/Y') ?? '—' }}</td>
                            <td class="text-end font-monospace small">
                                {{ $f->valeur_acquisition ? number_format($f->valeur_acquisition, 0, ',', ' ').' Ar' : '—' }}
                            </td>
                            <td class="text-center">{{ $f->qte_achetee }}</td>
                            <td class="text-end font-monospace small fw-semibold text-success">
                                {{ $f->valeur_acquisition ? number_format($f->valeur_totale, 0, ',', ' ').' Ar' : '—' }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('inventaire.show', $f->idfitaovana) }}"
                                       class="btn btn-outline-warning" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->peutModifier())
                                    <a href="{{ route('inventaire.edit', $f->idfitaovana) }}"
                                       class="btn btn-outline-secondary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->peutSupprimer())
                                    <form action="{{ route('inventaire.destroy', $f->idfitaovana) }}"
                                          method="POST"
                                          onsubmit="return confirm('Supprimer cet équipement ?')">
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
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-box fs-3 d-block mb-2"></i>Aucun équipement trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $fitaovanas->total() }} résultat(s)</small>
            {{ $fitaovanas->links() }}
        </div>
    </div>
</div>
@endsection
