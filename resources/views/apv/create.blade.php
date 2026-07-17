{{-- ============================================================ --}}
{{-- FICHIER : resources/views/apv/create.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Nouvel APV')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('apvs.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h1 class="h4 mb-0">Nouvel APV</h1>
    </div>

    <form action="{{ route('apvs.store') }}" method="POST">
        @csrf
        <div class="card shadow-sm">
            <div class="card-body row g-3">

                {{-- Code APV (saisie manuelle) --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold required">Code APV</label>
                    <input type="text" name="idapv" value="{{ old('idapv') }}"
                           class="form-control @error('idapv') is-invalid @enderror"
                           placeholder="Ex: APV01" autofocus>
                    @error('idapv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Libellé --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold required">Libellé</label>
                    <input type="text" name="libelle_apv" value="{{ old('libelle_apv') }}"
                           class="form-control @error('libelle_apv') is-invalid @enderror">
                    @error('libelle_apv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Faritra --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold required">Faritra</label>
                    <select name="idfaritra" class="form-select @error('idfaritra') is-invalid @enderror">
                        <option value="">— Choisir —</option>
                        @foreach($faritras as $f)
                            <option value="{{ $f->idfaritra }}" {{ old('idfaritra') == $f->idfaritra ? 'selected':'' }}>
                                {{ $f->libelle_faritra }}
                            </option>
                        @endforeach
                    </select>
                    @error('idfaritra')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('apvs.index') }}" class="btn btn-outline-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        </div>
    </form>
</div>
@endsection