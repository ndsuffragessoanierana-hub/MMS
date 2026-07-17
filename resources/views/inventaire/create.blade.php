
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/inventaire/create.blade.php       --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', "Nouvel équipement")
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('inventaire.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="h4 mb-0"><i class="bi bi-box-seam text-warning me-2"></i>Nouvel équipement</h1>
    </div>

    <form action="{{ route('inventaire.store') }}" method="POST">
        @csrf

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
                                   value="{{ old('denomination') }}"
                                   class="form-control @error('denomination') is-invalid @enderror"
                                   required>
                            @error('denomination')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">N° Inventaire</label>
                            <input type="text" name="no_inventaire"
                                   value="{{ old('no_inventaire') }}"
                                   class="form-control @error('no_inventaire') is-invalid @enderror">
                            @error('no_inventaire')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Référence</label>
                            <input type="text" name="reference"
                                   value="{{ old('reference') }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type d'équipement</label>
                            <select name="tf_id_type_fitaovana" class="form-select">
                                <option value="">— Aucun —</option>
                                @foreach($types as $t)
                                    <option value="{{ $t->id_type_fitaovana }}"
                                        {{ old('tf_id_type_fitaovana') == $t->id_type_fitaovana ? 'selected' : '' }}>
                                        {{ $t->id_type_fitaovana }} — {{ $t->libelle_type_fitaovana }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fournisseur</label>
                            <input type="text" name="fournisseur"
                                   value="{{ old('fournisseur') }}"
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
                                   value="{{ old('date_acquisition', now()->format('Y-m-d')) }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Quantité <span class="text-danger">*</span></label>
                            <input type="number" name="qte_achetee" min="1"
                                   value="{{ old('qte_achetee', 1) }}"
                                   class="form-control @error('qte_achetee') is-invalid @enderror"
                                   id="qte" required>
                            @error('qte_achetee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Valeur unitaire (Ar)</label>
                            <input type="number" name="valeur_acquisition" min="0" step="0.01"
                                   value="{{ old('valeur_acquisition', 0) }}"
                                   class="form-control text-end @error('valeur_acquisition') is-invalid @enderror"
                                   id="valeur">
                            @error('valeur_acquisition')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Valeur totale</label>
                            <div class="form-control bg-light fw-bold text-success font-monospace" id="valeurTotale">
                                0 Ar
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Remarque</label>
                            <textarea name="remarque" rows="2" class="form-control">{{ old('remarque') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('inventaire.index') }}" class="btn btn-outline-secondary">Annuler</a>
            <button type="submit" class="btn btn-warning">
                <i class="bi bi-check-lg me-1"></i>Enregistrer
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
const qteInput    = document.getElementById('qte');
const valeurInput = document.getElementById('valeur');
const totalDiv    = document.getElementById('valeurTotale');

function calculer() {
    const q = parseFloat(qteInput.value)    || 0;
    const v = parseFloat(valeurInput.value) || 0;
    totalDiv.textContent = new Intl.NumberFormat('fr').format(q * v) + ' Ar';
}
qteInput.addEventListener('input', calculer);
valeurInput.addEventListener('input', calculer);
calculer();
</script>
@endpush
@endsection