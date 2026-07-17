
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/type-fitaovana/create.blade.php --}}
{{-- ============================================================ --}}

@extends('layouts.app')
@section('title', "Nouveau type d'équipement")
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('type-fitaovanas.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="h4 mb-0">Nouveau type d'équipement</h1>
    </div>

    <form action="{{ route('type-fitaovanas.store') }}" method="POST">
        @csrf
        <div class="card shadow-sm">
            <div class="card-body row g-3">

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                    <input type="text" name="id_type_fitaovana"
                           value="{{ old('id_type_fitaovana') }}"
                           class="form-control @error('id_type_fitaovana') is-invalid @enderror"
                           maxlength="5"
                           placeholder="ex: MOB"
                           style="text-transform:uppercase">
                    @error('id_type_fitaovana')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">5 caractères max</div>
                </div>

                <div class="col-md-10">
                    <label class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
                    <input type="text" name="libelle_type_fitaovana"
                           value="{{ old('libelle_type_fitaovana') }}"
                           class="form-control @error('libelle_type_fitaovana') is-invalid @enderror"
                           placeholder="ex: Mobilier">
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
// Forcer majuscules sur le code
document.querySelector('input[name="id_type_fitaovana"]').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
@endpush
@endsection