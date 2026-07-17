
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/type-fitaovana/edit.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', "Modifier le type d'équipement")
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('type-fitaovanas.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="h4 mb-0">Modifier : {{ $typeFitaovana->libelle_type_fitaovana }}</h1>
    </div>

    <form action="{{ route('type-fitaovanas.update', $typeFitaovana->id_type_fitaovana) }}" method="POST">
        @csrf @method('PUT')
        <div class="card shadow-sm">
            <div class="card-body row g-3">

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                    <input type="text" name="id_type_fitaovana"
                           value="{{ old('id_type_fitaovana', $typeFitaovana->id_type_fitaovana) }}"
                           class="form-control @error('id_type_fitaovana') is-invalid @enderror"
                           maxlength="5"
                           style="text-transform:uppercase"
                           {{ $typeFitaovana->fitaovanas_count > 0 ? 'readonly' : '' }}>
                    @error('id_type_fitaovana')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($typeFitaovana->fitaovanas_count > 0)
                        <div class="form-text text-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ $typeFitaovana->fitaovanas_count }} équipement(s) — code non modifiable
                        </div>
                    @else
                        <div class="form-text text-muted">Modifiable (aucun équipement rattaché)</div>
                    @endif
                </div>

                <div class="col-md-10">
                    <label class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
                    <input type="text" name="libelle_type_fitaovana"
                           value="{{ old('libelle_type_fitaovana', $typeFitaovana->libelle_type_fitaovana) }}"
                           class="form-control @error('libelle_type_fitaovana') is-invalid @enderror">
                    @error('libelle_type_fitaovana')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('type-fitaovanas.index') }}" class="btn btn-outline-secondary">Annuler</a>
            <button type="submit" class="btn btn-warning">
                <i class="bi bi-check-lg me-1"></i>Enregistrer
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelector('input[name="id_type_fitaovana"]')?.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
@endpush
@endsection