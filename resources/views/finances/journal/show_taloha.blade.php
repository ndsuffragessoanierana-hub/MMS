<?php // ===================================================================
// FICHIER : resources/views/finances/journal/show.blade.php
// =================================================================== ?>
{{-- ========== finances/journal/show.blade.php ========== --}}
@extends('layouts.app')
@section('title', 'Journal — ' . $journal->periode)
@section('content')
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="d-flex gap-3 align-items-center">
            <a href="{{ route('finances.journals.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0 text-success">Journal — {{ $journal->periode }}</h1>
                <small class="text-muted">{{ $details->count() }} écriture(s)</small>
            </div>
        </div>
        {{-- Soldes --}}
        <div class="d-flex gap-3">
            <div class="text-center">
                <div class="small text-muted">BNI</div>
                <div class="fw-bold font-monospace">{{ number_format($journal->journal_solde_bni, 0, ',', ' ') }} Ar</div>
            </div>
            <div class="text-center">
                <div class="small text-muted">BFV</div>
                <div class="fw-bold font-monospace">{{ number_format($journal->journal_solde_bfv, 0, ',', ' ') }} Ar</div>
            </div>
            <div class="text-center">
                <div class="small text-muted">Caisse</div>
                <div class="fw-bold font-monospace">{{ number_format($journal->journal_solde_caisse, 0, ',', ' ') }} Ar</div>
            </div>
            <div class="text-center border-start ps-3">
                <div class="small text-muted">Total solde</div>
                <div class="fw-bold text-success font-monospace fs-5">
                    {{ number_format($journal->solde_total, 0, ',', ' ') }} Ar
                </div>
            </div>
        </div>
    </div>

    {{-- Résumé Recettes / Dépenses --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted">Total Recettes</div>
                        <div class="fs-4 fw-bold text-success font-monospace">
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
                        <div class="fs-4 fw-bold text-danger font-monospace">
                            {{ number_format($totalDepenses, 0, ',', ' ') }} Ar
                        </div>
                    </div>
                    <i class="bi bi-arrow-up-circle-fill text-danger fs-2 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @php $resultat = $totalRecettes - $totalDepenses; @endphp
            <div class="card border-{{ $resultat >= 0 ? 'success' : 'danger' }} shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted">Résultat du mois</div>
                        <div class="fs-4 fw-bold text-{{ $resultat >= 0 ? 'success' : 'danger' }} font-monospace">
                            {{ ($resultat >= 0 ? '+' : '') . number_format($resultat, 0, ',', ' ') }} Ar
                        </div>
                    </div>
                    <i class="bi bi-{{ $resultat >= 0 ? 'graph-up' : 'graph-down' }}-arrow fs-2 opacity-25
                       text-{{ $resultat >= 0 ? 'success' : 'danger' }}"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des écritures --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="bi bi-list-ul me-2"></i>Écritures du mois</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Libellé</th>
                            <th>Rubrique</th>
                            <th>Chapitre</th>
                            <th>Mode paiement</th>
                            <th>Compte</th>
                            <th class="text-end">Montant</th>
                            @if(auth()->user()->peutSupprimer())
                            <th class="text-center">Suppr.</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentChap = null; @endphp
                        @forelse($details as $d)
                            {{-- Séparateur de chapitre --}}
                            @if($d->rubrique?->chap_code !== $currentChap)
                                @php $currentChap = $d->rubrique?->chap_code; @endphp
                                <tr class="table-{{ str_starts_with($currentChap ?? '', 'A') ? 'success' : 'danger' }} opacity-75">
                                    <td colspan="{{ auth()->user()->peutSupprimer() ? 8 : 7 }}" class="fw-bold small py-1">
                                        {{ $d->rubrique?->chapitre?->chap_code }} —
                                        {{ $d->rubrique?->chapitre?->chap_libelle }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="small">{{ $d->j_detail_date->format('d/m/Y') }}</td>
                                <td>{{ $d->j_detail_libelle }}</td>
                                <td class="small">
                                    <code>{{ $d->rub_rubrique_id }}</code>
                                    <span class="text-muted">{{ $d->rubrique?->rubrique_libelle }}</span>
                                </td>
                                <td class="small text-muted">{{ $d->rubrique?->chap_code }}</td>
                                
                                <td class="small">
                                    @if($d->j_detail_mode_paie)
                                        @php
                                            $modesLabels = [
                                                'ESP' => ['label' => 'ESPECE',   'color' => 'secondary'],
                                                'BFV' => ['label' => 'BRED',     'color' => 'danger'],
                                                'BNI' => ['label' => 'BNI',      'color' => 'primary'],
                                                'VIR' => ['label' => 'VIREMENT', 'color' => 'info'],
                                                'MOB' => ['label' => 'MOBILE',   'color' => 'success'],
                                            ];
                                            $mode = $modesLabels[$d->j_detail_mode_paie] ?? ['label' => $d->j_detail_mode_paie, 'color' => 'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $mode['color'] }}-subtle text-{{ $mode['color'] }} border border-{{ $mode['color'] }}-subtle">
                                            {{ $mode['label'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $d->compte?->libelle_compte ?? '—' }}</td>
                                <td class="text-end font-monospace fw-semibold
                                    text-{{ str_starts_with($d->rubrique?->chap_code ?? '', 'A') ? 'success' : 'danger' }}">
                                    {{ number_format($d->j_detail_montant, 0, ',', ' ') }} Ar
                                </td>
                                @if(auth()->user()->peutSupprimer())
                                <td class="text-center">
                                    <form action="{{ route('finances.ecritures.destroy', $d->j_detail_numero) }}"
                                          method="POST"
                                          onsubmit="return confirm('Supprimer cette écriture ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm py-0 px-1">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->peutSupprimer() ? 8 : 7 }}"
                                class="text-center text-muted py-3">
                                Aucune écriture pour ce mois.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($details->count())
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="{{ auth()->user()->peutSupprimer() ? 6 : 5 }}"></td>
                            <td class="text-end">
                                <div class="text-success font-monospace">
                                    ↓ {{ number_format($totalRecettes, 0, ',', ' ') }} Ar
                                </div>
                                <div class="text-danger font-monospace">
                                    ↑ {{ number_format($totalDepenses, 0, ',', ' ') }} Ar
                                </div>
                            </td>
                            @if(auth()->user()->peutSupprimer())<td></td>@endif
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Formulaire d'ajout d'écriture --}}
    @if(auth()->user()->peutAjouter())
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <i class="bi bi-plus-circle me-2"></i>Nouvelle écriture
        </div>
        <div class="card-body">
            <form action="{{ route('finances.journals.ecritures.store', $journal->journal_id) }}" method="POST">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold required">Date</label>
                        <input type="date" name="j_detail_date"
                               value="{{ old('j_detail_date', now()->format('Y-m-d')) }}"
                               class="form-control form-control-sm @error('j_detail_date') is-invalid @enderror">
                        @error('j_detail_date')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold required">Libellé</label>
                        <input type="text" name="j_detail_libelle"
                               value="{{ old('j_detail_libelle') }}"
                               class="form-control form-control-sm @error('j_detail_libelle') is-invalid @enderror"
                               placeholder="Description de l'écriture">
                        @error('j_detail_libelle')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold required">Rubrique</label>
                        <select name="rub_rubrique_id"
                                class="form-select form-select-sm @error('rub_rubrique_id') is-invalid @enderror">
                            <option value="">— Choisir —</option>
                            @foreach($rubriques->groupBy('chap_code') as $chap => $rubs)
                                <optgroup label="{{ $rubs->first()->chapitre?->chap_libelle ?? $chap }}">
                                    @foreach($rubs as $r)
                                        <option value="{{ $r->rubrique_id }}"
                                            {{ old('rub_rubrique_id') == $r->rubrique_id ? 'selected':'' }}>
                                            {{ $r->rubrique_id }} — {{ $r->rubrique_libelle }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('rub_rubrique_id')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold">Mode paiement</label>
                        <select name="j_detail_mode_paie" class="form-select form-select-sm">
                            <option value="">—</option>
                            <option value="ESP" {{ old('j_detail_mode_paie') == 'ESP' ? 'selected':'' }}>ESPECE</option>
                            <option value="BFV" {{ old('j_detail_mode_paie') == 'BFV' ? 'selected':'' }}>BRED</option>
                            <option value="BNI" {{ old('j_detail_mode_paie') == 'BNI' ? 'selected':'' }}>BNI</option>
                            <option value="VIR" {{ old('j_detail_mode_paie') == 'VIR' ? 'selected':'' }}>VIREMENT</option>
                            <option value="MOB" {{ old('j_detail_mode_paie') == 'MOB' ? 'selected':'' }}>MOBILE</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Compte</label>
                        <select name="cpt_no_compte" class="form-select form-select-sm">
                            <option value="">— Aucun —</option>
                            @foreach($comptes as $c)
                                <option value="{{ $c->no_compte }}"
                                    {{ old('cpt_no_compte') == $c->no_compte ? 'selected':'' }}>
                                    {{ $c->no_compte }} — {{ $c->libelle_compte }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold required">Montant (Ar)</label>
                        <input type="number" name="j_detail_montant"
                               value="{{ old('j_detail_montant') }}"
                               step="0.01" min="0"
                               class="form-control form-control-sm text-end @error('j_detail_montant') is-invalid @enderror"
                               placeholder="0">
                        @error('j_detail_montant')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>Ajouter l'écriture
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
