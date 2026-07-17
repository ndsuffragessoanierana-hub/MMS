{{-- ============================================================ --}}
{{-- FICHIER : resources/views/faritra/show.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', $faritra->libelle_faritra)
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('faritraS.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="h3 mb-0">{{ $faritra->libelle_faritra }}</h1>
                @if($faritra->sigle)<span class="badge bg-primary-subtle text-primary">{{ $faritra->sigle }}</span>@endif
            </div>
        </div>
        @if(auth()->user()->peutModifier())
        <a href="{{ route('faritraS.edit', $faritra->idfaritra) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Informations</div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-5">Saint Patron</dt>
                        <dd class="col-7">{{ $faritra->st_patron ?? '—' }}</dd>
                        <dt class="col-5">Ordre d'affichage</dt>
                        <dd class="col-7">{{ $faritra->num_ordre_faritra ?? '—' }}</dd>
                        <dt class="col-5">Nombre de fidèles</dt>
                        <dd class="col-7"><span class="badge bg-info-subtle text-info">{{ $faritra->fideles_count }}</span></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between">
                    <span>APV (Sous-secteurs)</span>
                    @if(auth()->user()->peutAjouter())
                    <a href="{{ route('apvs.create') }}" class="text-white small">+ Ajouter</a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($faritra->apvs->isEmpty())
                        <p class="text-muted p-3 mb-0">Aucun APV pour ce Faritra.</p>
                    @else
                    <ul class="list-group list-group-flush">
                        @foreach($faritra->apvs as $a)
                        <li class="list-group-item small">
                            <a href="{{ route('apvs.show', $a->idapv) }}" class="text-decoration-none">
                                {{ $a->libelle_apv }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

