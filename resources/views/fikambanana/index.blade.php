{{-- ============================================================ --}}
{{-- FICHIER : resources/views/fikambanana/index.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Fikambanana / Vaomiera')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-person-lines-fill text-primary me-2"></i>Fikambanana / Vaomiera</h1>
        @if(auth()->user()->peutAjouter())
        <a href="{{ route('fikambananas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nouvelle association
        </a>
        @endif
    </div>

    <div class="row g-3">
        @forelse($fikambananas as $fik)
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="{{ route('fikambananas.show', $fik->idfikambanana) }}" class="text-decoration-none">
                            {{ $fik->libelle_fikambanana }}
                        </a>
                    </h5>
                    @if($fik->st_patron)
                        <p class="text-muted small mb-2"><i class="bi bi-star-fill text-warning"></i> {{ $fik->st_patron }}</p>
                    @endif
                    <span class="badge bg-info-subtle text-info">{{ $fik->fideles_count }} membre(s)</span>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end gap-1">
                    <a href="{{ route('fikambananas.show', $fik->idfikambanana) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                    @if(auth()->user()->peutModifier())
                    <a href="{{ route('fikambananas.edit', $fik->idfikambanana) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center text-muted py-4">Aucune association enregistrée.</div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
