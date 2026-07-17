
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/apv/show.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', $apv->libelle_apv)
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('apvs.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="h3 mb-0">{{ $apv->libelle_apv }}</h1>
                <small class="text-muted">Faritra : {{ $apv->faritra?->libelle_faritra ?? '—' }}</small>
            </div>
        </div>
        @if(auth()->user()->peutModifier())
        <a href="{{ route('apvs.edit', $apv->idapv) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            Fidèles de cet APV ({{ $apv->fideles_count }})
        </div>
        <div class="card-body p-0">
            @if($apv->fideles->isEmpty())
                <p class="text-muted p-3 mb-0">Aucun fidèle rattaché.</p>
            @else
            <ul class="list-group list-group-flush">
                @foreach($apv->fideles as $f)
                <li class="list-group-item small">
                    <a href="{{ route('fideles.show', $f->matricule) }}" class="text-decoration-none">
                        {{ $f->nom_complet }}
                    </a>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection
