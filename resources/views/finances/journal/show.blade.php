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
            <a href="{{ route('finances.journals.pdf', $journal->journal_id) }}"
                class="btn btn-outline-danger btn-sm" target="_blank">
                <i class="bi bi-file-pdf me-1"></i>PDF
            </a>
        </div>
        {{-- Soldes --}}
        <div class="d-flex gap-3">
            <div class="text-center">
                <div class="small text-muted">BNI</div>
                <div class="fw-bold font-monospace">{{ number_format($journal->journal_solde_bni, 0, ',', ' ') }} Ar</div>
            </div>
            <div class="text-center">
                <div class="small text-muted">BRED</div>
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
                <table class="table table-sm table-hover mb-0" style="table-layout:fixed;">
                    <thead class="table-light">

                        <tr>
                            <th style="width:90px;">Date</th>
                            <th style="width:250px;">Libellé</th>
                            <th style="width:180px;">Rubrique</th>
                            <th style="width:80px;">Chapitre</th>
                            <th style="width:100px;">Mode paiement</th>
                            <th style="width:120px;">Compte</th>
                            <th style="width:120px;" class="text-end">Montant</th>
                            <th style="width:60px;" class="text-center">Versement</th>
                            @if(auth()->user()->peutSupprimer())
                            <th style="width:60px;" class="text-center">Suppr.</th>
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
                                    <td colspan="{{ auth()->user()->peutSupprimer() ? 9 : 8 }}" class="fw-bold small py-1">
                                        {{ $d->rubrique?->chapitre?->chap_code }} —
                                        {{ $d->rubrique?->chapitre?->chap_libelle }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="small">{{ $d->j_detail_date->format('d/m/Y') }}</td>
                                <td class="small text-truncate" style="max-width:250px;" title="{{ $d->j_detail_libelle }}">
                                    {{ $d->j_detail_libelle }}
                                </td>
                                <td class="small text-truncate" style="max-width:180px;" title="{{ $d->rubrique?->rubrique_libelle }}">
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

                                <td class="text-center">
                                    @if($d->rub_rubrique_id == 502 || $d->rubrique?->rubrique_libelle === 'versement_especes')
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm py-0 px-1 btn-versement"
                                                data-numero="{{ $d->j_detail_numero }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalVersement">
                                            <i class="bi bi-cash-stack"></i>
                                        </button>
                                    @endif
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
                            <td colspan="{{ auth()->user()->peutSupprimer() ? 9 : 8 }}"
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

{{-- Modal Détail Versement --}}
<div class="modal fade" id="modalVersement" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Détail du versement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Libellé</th>
                            <th class="text-end">Montant</th>
                            <th>Remarque</th>
                            <th style="width:100px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="versementTableBody">
                        {{-- Rempli dynamiquement en JS --}}
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-end" id="versementTotal">0 Ar</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>

                <hr>

                <form id="formAddVersement" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Libellé</label>
                        <input type="text" name="libelle" id="versementLibelle" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Montant (Ar)</label>
                        <input type="number" name="montant" id="versementMontant" step="0.01" min="0" class="form-control form-control-sm text-end" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Remarque</label>
                        <input type="text" name="remarque" id="versementRemarque" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-plus-lg"></i> Ajouter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let currentNumero = null;

    const modal = document.getElementById('modalVersement');
    const tbody = document.getElementById('versementTableBody');
    const totalEl = document.getElementById('versementTotal');
    const formAdd = document.getElementById('formAddVersement');

    // Ouverture du modal : récupère le numero et charge les lignes
    document.querySelectorAll('.btn-versement').forEach(btn => {
        btn.addEventListener('click', function () {
            currentNumero = this.dataset.numero;
            chargerLignes();
        });
    });

    function formatMontant(val) {
        return new Intl.NumberFormat('fr-FR').format(val) + ' Ar';
    }

    function chargerLignes() {
        fetch(`/finances/versements/${currentNumero}`)
            .then(res => res.json())
            .then(lignes => {
                tbody.innerHTML = '';
                let total = 0;

                lignes.forEach(l => {
                    total += parseFloat(l.montant);
                    tbody.innerHTML += `
                        <tr data-id="${l.id}">
                            <td class="libelle-cell">${l.libelle}</td>
                            <td class="text-end montant-cell">${formatMontant(l.montant)}</td>
                            <td class="remarque-cell">${l.remarque ?? ''}</td>
                            <td class="text-center">
                                <button class="btn btn-outline-secondary btn-sm py-0 px-1 btn-edit-versement"
                                    data-id="${l.id}"
                                    data-libelle="${l.libelle}"
                                    data-montant="${l.montant}"
                                    data-remarque="${l.remarque ?? ''}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm py-0 px-1 btn-delete-versement"
                                    data-id="${l.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                totalEl.textContent = formatMontant(total);
                attacherActions();
            });
    }

    function attacherActions() {
        // Suppression
        document.querySelectorAll('.btn-delete-versement').forEach(btn => {
            btn.addEventListener('click', function () {
                if (!confirm('Supprimer cette ligne ?')) return;
                const id = this.dataset.id;

                fetch(`/finances/versements/${currentNumero}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(res => res.json())
                .then(() => chargerLignes());
            });
        });

        // Édition (remplace la ligne par un formulaire inline)
        document.querySelectorAll('.btn-edit-versement').forEach(btn => {
            btn.addEventListener('click', function () {
                const row = this.closest('tr');
                const id = this.dataset.id;
                const libelle = this.dataset.libelle;
                const montant = this.dataset.montant;
                const remarque = this.dataset.remarque;

                row.innerHTML = `
                    <td><input type="text" class="form-control form-control-sm edit-libelle" value="${libelle}"></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm text-end edit-montant" value="${montant}"></td>
                    <td><input type="text" class="form-control form-control-sm edit-remarque" value="${remarque}"></td>
                    <td class="text-center">
                        <button class="btn btn-success btn-sm py-0 px-1 btn-save-versement" data-id="${id}">
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </td>
                `;

                row.querySelector('.btn-save-versement').addEventListener('click', function () {
                    const newLibelle = row.querySelector('.edit-libelle').value;
                    const newMontant = row.querySelector('.edit-montant').value;
                    const newRemarque = row.querySelector('.edit-remarque').value;

                    fetch(`/finances/versements/${currentNumero}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            libelle: newLibelle,
                            montant: newMontant,
                            remarque: newRemarque
                        })
                    })
                    .then(res => res.json())
                    .then(() => chargerLignes());
                });
            });
        });
    }

    // Ajout d'une nouvelle ligne
    formAdd.addEventListener('submit', function (e) {
        e.preventDefault();

        const libelle = document.getElementById('versementLibelle').value;
        const montant = document.getElementById('versementMontant').value;
        const remarque = document.getElementById('versementRemarque').value;

        fetch(`/finances/versements/${currentNumero}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ libelle, montant, remarque })
        })
        .then(res => res.json())
        .then(() => {
            formAdd.reset();
            chargerLignes();
        });
    });

    // Reset du modal à la fermeture
    modal.addEventListener('hidden.bs.modal', function () {
        tbody.innerHTML = '';
        totalEl.textContent = '0 Ar';
        formAdd.reset();
    });
});
</script>
@endpush
@endsection
