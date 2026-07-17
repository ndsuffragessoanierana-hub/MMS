
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/agenda/create.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Nouvel événement')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h1 class="h4 mb-0">Nouvel événement</h1>
    </div>

    <form action="{{ route('agenda.store') }}" method="POST">
        @csrf
        <div class="card shadow-sm">
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold required">Date</label>
                    <input type="date" name="date_agenda" value="{{ old('date_agenda', now()->format('Y-m-d')) }}"
                           class="form-control @error('date_agenda') is-invalid @enderror">
                    @error('date_agenda')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold required">Libellé</label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}"
                           class="form-control @error('libelle') is-invalid @enderror">
                    @error('libelle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Observation</label>
                    <textarea name="observation" rows="3" class="form-control">{{ old('observation') }}</textarea>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        </div>
    </form>
</div>
@endsection
