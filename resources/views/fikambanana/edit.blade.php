
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/fikambanana/edit.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Modifier association')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('fikambananas.show', $fikambanana->idfikambanana) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h1 class="h4 mb-0">Modifier : {{ $fikambanana->libelle_fikambanana }}</h1>
    </div>

    <form action="{{ route('fikambananas.update', $fikambanana->idfikambanana) }}" method="POST">
        @csrf @method('PUT')
        <div class="card shadow-sm">
            <div class="card-body row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold required">Libellé</label>
                    <input type="text" name="libelle_fikambanana" value="{{ old('libelle_fikambanana', $fikambanana->libelle_fikambanana) }}"
                           class="form-control @error('libelle_fikambanana') is-invalid @enderror">
                    @error('libelle_fikambanana')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Saint Patron</label>
                    <input type="text" name="st_patron" value="{{ old('st_patron', $fikambanana->st_patron) }}" class="form-control">
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('fikambananas.show', $fikambanana->idfikambanana) }}" class="btn btn-outline-secondary">Annuler</a>
            <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        </div>
    </form>
</div>
@endsection
