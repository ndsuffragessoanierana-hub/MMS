@extends('layouts.app')
@section('title', 'Modifier équipement')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('inventaire.show', $inventaire->idfitaovana) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 mb-0">Modifier : {{ $inventaire->denomination }}</h1>
            @if($inventaire->no_inventaire)
                <small class="text-muted">N° {{ $inventaire->no_inventaire }}</small>
            @endif
        </div>
    </div>

    <form action="{{ route('inventaire.update', $inventaire->idfitaovana) }}" method="POST">
        @csrf @method('PUT')

        <div class="row g-4">

            {{-- Identification --}}
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark fw-semibold">
                        <i class="bi bi-box-seam me-2"></i>Identification
                    </div>
                    <div class="card-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dénomination <span class="text-danger">*</span></label>
                            <input type="text" name="denomination"
                                   value="{{ old('denomination', $inventaire->denomination) }}"
                                   class="form-control @error('denomination') is-invalid @enderror">
                            @error('denomination')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">N° Inventaire</label>
                            <input type="text" name="no_inventaire"
                                   value="{{ old('no_inventaire', $inventaire->no_inventaire) }}"
                                   class="form-control @error('no_inventaire') is-invalid @enderror">
                            @error('no_inventaire')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Référence</label>
                            <input type="text" name="reference"
                                   value="{{ old('reference', $inventaire->reference) }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type d'équipement</label>
                            <select name="tf_id_type_fitaovana" class="form-select">
                                <option value="">— Aucun —</option>
                                @foreach($types as $t)
                                    <option value="{{ $t->id_type_fitaovana }}"
                                        {{ old('tf_id_type_fitaovana', $inventaire->tf_id_type_fitaovana) == $t->id_type_fitaovana ? 'selected' : '' }}>
                                        {{ $t->id_type_fitaovana }} — {{ $t->libelle_type_fitaovana }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fournisseur</label>
                            <input type="text" name="fournisseur"
                                   value="{{ old('fournisseur', $inventaire->fournisseur) }}"
                                   class="form-control">
                        </div>

                    </div>
                </div>
            </div>

            {{-- Acquisition --}}
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-calendar-check me-2"></i>Acquisition
                    </div>
                    <div class="card-body row g-3">

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date d'acquisition</label>
                            <input type="date" name="date_acquisition"
                                   value="{{ old('date_acquisition', $inventaire->date_acquisition?->format('Y-m-d')) }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Quantité <span class="text-danger">*</span></label>
                            <input type="number" name="qte_achetee" min="1"
                                   value="{{ old('qte_achetee', $inventaire->qte_achetee) }}"
                                   class="form-control @error('qte_achetee') is-invalid @enderror">
                            @error('qte_achetee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Valeur unitaire (Ar)</label>
                            <input type="number" name="valeur_acquisition" min="0" step="0.01"
                                   value="{{ old('valeur_acquisition', $inventaire->valeur_acquisition) }}"
                                   class="form-control @error('valeur_acquisition') is-invalid @enderror">
                            @error('valeur_acquisition')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Valeur totale</label>
                            <div class="form-control bg-light text-success fw-bold font-monospace" id="valeurTotale">
                                {{ $inventaire->valeur_acquisition
                                    ? number_format($inventaire->valeur_totale, 0, ',', ' ') . ' Ar'
                                    : '—' }}
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Remarque</label>
                            <textarea name="remarque" rows="2" class="form-control">{{ old('remarque', $inventaire->remarque) }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('inventaire.show', $inventaire->idfitaovana) }}" class="btn btn-outline-secondary">
                Annuler
            </a>
            <button type="submit" class="btn btn-warning">
                <i class="bi bi-check-lg me-1"></i>Enregistrer les modifications
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
// Calcul automatique valeur totale
const qte    = document.querySelector('input[name="qte_achetee"]');
const valeur = document.querySelector('input[name="valeur_acquisition"]');
const total  = document.getElementById('valeurTotale');

function calculerTotal() {
    const q = parseFloat(qte.value) || 0;
    const v = parseFloat(valeur.value) || 0;
    if (q > 0 && v > 0) {
        total.textContent = new Intl.NumberFormat('fr').format(q * v) + ' Ar';
        total.classList.add('text-success');
    } else {
        total.textContent = '—';
    }
}

qte.addEventListener('input', calculerTotal);
valeur.addEventListener('input', calculerTotal);
</script>
@endpush
@endsection
