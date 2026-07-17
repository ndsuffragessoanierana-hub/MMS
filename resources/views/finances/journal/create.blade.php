{{-- ============================================================ --}}
{{-- FICHIER : resources/views/finances/journal/create.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Ouvrir un nouveau journal')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('finances.journals.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="h4 mb-0"><i class="bi bi-journal-plus text-success me-2"></i>Ouvrir un nouveau journal</h1>
    </div>

    <form action="{{ route('finances.journals.store') }}" method="POST">
        @csrf
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white fw-semibold">
                <i class="bi bi-calendar3 me-2"></i>Période du journal
            </div>
            <div class="card-body row g-3">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Mois <span class="text-danger">*</span></label>
                    <select name="journal_mois"
                            class="form-select @error('journal_mois') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach(\App\Models\Mois::all() as $m)
                            <option value="{{ $m->numero }}"
                                {{ old('journal_mois', $moisSuggere['mois']) == $m->numero ? 'selected' : '' }}>
                                {{ $m->libelle_mois_fr }}
                            </option>
                        @endforeach
                    </select>
                    @error('journal_mois')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Année <span class="text-danger">*</span></label>
                    <input type="number" name="journal_annee"
                           value="{{ old('journal_annee', $moisSuggere['annee']) }}"
                           class="form-control @error('journal_annee') is-invalid @enderror"
                           min="2000" max="2100" required>
                    @error('journal_annee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="alert alert-info py-2 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Saisissez les soldes de début de période (reportés du mois précédent).
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Solde BNI (Ar)</label>
                    <input type="number" name="journal_solde_bni"
                           value="{{ old('journal_solde_bni', $dernier?->journal_solde_bni ?? 0) }}"
                           class="form-control text-end" min="0" step="0.01">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Solde BRED (Ar)</label>
                    <input type="number" name="journal_solde_bfv"
                           value="{{ old('journal_solde_bfv', $dernier?->journal_solde_bfv ?? 0) }}"
                           class="form-control text-end" min="0" step="0.01">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Solde Caisse (Ar)</label>
                    <input type="number" name="journal_solde_caisse"
                           value="{{ old('journal_solde_caisse', $dernier?->journal_solde_caisse ?? 0) }}"
                           class="form-control text-end" min="0" step="0.01">
                </div>

            </div>
        </div>

        @if($dernier)
        <div class="alert alert-secondary mt-3 py-2">
            <i class="bi bi-clock-history me-2"></i>
            Dernier journal : <strong>{{ $dernier->periode }}</strong>
            — Solde total : <strong class="font-monospace">{{ number_format($dernier->solde_total, 0, ',', ' ') }} Ar</strong>
        </div>
        @endif

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('finances.journals.index') }}" class="btn btn-outline-secondary">Annuler</a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-lg me-1"></i>Ouvrir le journal
            </button>
        </div>
    </form>

</div>
@endsection

