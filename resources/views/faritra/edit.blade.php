{{-- ============================================================ --}}
{{-- FICHIER : resources/views/faritra/edit.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Modifier Faritra')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('faritraS.show', $faritra->idfaritra) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h1 class="h4 mb-0">Modifier : {{ $faritra->libelle_faritra }}</h1>
    </div>

    <form action="{{ route('faritraS.update', $faritra->idfaritra) }}" method="POST">
        @csrf @method('PUT')
        <div class="card shadow-sm">
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold required">Libellé</label>
                    <input type="text" name="libelle_faritra" value="{{ old('libelle_faritra', $faritra->libelle_faritra) }}"
                           class="form-control @error('libelle_faritra') is-invalid @enderror">
                    @error('libelle_faritra')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sigle</label>
                    <input type="text" name="sigle" value="{{ old('sigle', $faritra->sigle) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Ordre</label>
                    <input type="number" name="num_ordre_faritra" value="{{ old('num_ordre_faritra', $faritra->num_ordre_faritra) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Saint Patron</label>
                    <input type="text" name="st_patron" value="{{ old('st_patron', $faritra->st_patron) }}" class="form-control">
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('faritraS.show', $faritra->idfaritra) }}" class="btn btn-outline-secondary">Annuler</a>
            <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        </div>
    </form>
</div>
@endsection
