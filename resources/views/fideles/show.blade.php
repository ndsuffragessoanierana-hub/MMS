<?php
// =============================================================================
// FICHIER : resources/views/fideles/show.blade.php
// =============================================================================
?>
{{-- ========== show.blade.php ========== --}}
@extends('layouts.app')
@section('title', $fidele->nom_complet)
@section('content')
<div class="container-fluid py-4">

    {{-- En-tête fiche --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('fideles.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0">{{ $fidele->nom_complet }}</h1>
                @if($fidele->nom_bapteme)
                    <p class="text-muted mb-0 fst-italic">Nom de baptême : {{ $fidele->nom_bapteme }}</p>
                @endif
                <code class="small">{{ $fidele->matricule }}</code>
                &nbsp;
                @if($fidele->est_decede)
                    <span class="badge bg-secondary">Décédé(e)</span>
                @elseif($fidele->quitte == 'O')
                    <span class="badge bg-warning text-dark">Parti(e)</span>
                @else
                    <span class="badge bg-success">Actif(ve)</span>
                @endif
            </div>
        </div>
        @if(auth()->user()->peutModifier())
        <a href="{{ route('fideles.edit', $fidele->matricule) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        @endif
    </div>

    <div class="row g-4">

        {{-- Identité --}}
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white"><i class="bi bi-person me-2"></i>Identité</div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-5">Sexe</dt>
                        <dd class="col-7">{{ $fidele->sexe == 'M' ? 'Masculin' : ($fidele->sexe == 'F' ? 'Féminin' : '—') }}</dd>
                        <dt class="col-5">Date de naissance</dt>
                        <dd class="col-7">{{ $fidele->date_naissance?->format('d/m/Y') ?? '—' }} @if($fidele->age) <span class="text-muted">({{ $fidele->age }} ans)</span>@endif</dd>
                        <dt class="col-5">Lieu de naissance</dt>
                        <dd class="col-7">{{ $fidele->lieu_naissance ?? '—' }}</dd>
                        <dt class="col-5">Profession</dt>
                        <dd class="col-7">{{ $fidele->profession ?? '—' }}</dd>
                        <dt class="col-5">Père</dt>
                        <dd class="col-7">{{ $fidele->nom_pere ?? '—' }}</dd>
                        <dt class="col-5">Mère</dt>
                        <dd class="col-7">{{ $fidele->nom_mere ?? '—' }}</dd>
                        <dt class="col-5">N° Famille</dt>
                        <dd class="col-7">{{ $fidele->numero_famille ?? '—' }}</dd>
                        <dt class="col-5">N° Registre</dt>
                        <dd class="col-7">{{ $fidele->numero_registre ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Sacrements --}}
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-warning text-dark"><i class="bi bi-stars me-2"></i>Sacrements reçus</div>
                <div class="card-body">
                    @php
                        $sacrements = [
                            'Baptême'       => $fidele->date_bapteme,
                            '1ère Confession'=> $fidele->date_confesse,
                            '1ère Communion' => $fidele->date_communion,
                            'Confirmation'  => $fidele->date_confirmation,
                            'Mariage'       => $fidele->date_mariage,
                            'Ordination'    => $fidele->date_ordination,
                        ];
                    @endphp
                    <ul class="list-group list-group-flush small">
                        @foreach($sacrements as $label => $date)
                        <li class="list-group-item d-flex justify-content-between px-0 py-1">
                            <span>{{ $label }}</span>
                            @if($date)
                                <span class="badge bg-success-subtle text-success">{{ $date->format('d/m/Y') }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </li>
                        @endforeach
                        @if($fidele->date_deces)
                        <li class="list-group-item d-flex justify-content-between px-0 py-1">
                            <span class="text-danger">Décès</span>
                            <span class="badge bg-secondary">{{ $fidele->date_deces->format('d/m/Y') }}</span>
                        </li>
                        @endif
                    </ul>
                    @if($fidele->lieu_bapteme || $fidele->nom_pretre)
                    <div class="mt-2 small text-muted">
                        @if($fidele->lieu_bapteme) Lieu baptême : {{ $fidele->lieu_bapteme }}<br>@endif
                        @if($fidele->nom_pretre)   Prêtre : {{ $fidele->nom_pretre }}<br>@endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Paroisse --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white"><i class="bi bi-geo-alt me-2"></i>Paroisse</div>
                <div class="card-body small">
                    <dl class="row mb-0">
                        <dt class="col-5">Faritra</dt>
                        <dd class="col-7">{{ $fidele->faritra?->libelle_faritra ?? '—' }}</dd>
                        <dt class="col-5">APV</dt>
                        <dd class="col-7">{{ $fidele->apv?->libelle_apv ?? '—' }}</dd>
                        <dt class="col-5">Date d'arrivée</dt>
                        <dd class="col-7">{{ $fidele->date_arrivee?->format('d/m/Y') ?? '—' }}</dd>
                        <dt class="col-5">Date intégration</dt>
                        <dd class="col-7">{{ $fidele->date_integration?->format('d/m/Y') ?? '—' }}</dd>
                        <dt class="col-5">Adresse</dt>
                        <dd class="col-7">{{ $fidele->adresse ?? '—' }}</dd>
                    </dl>
                    @if($fidele->observation)
                        <div class="mt-2 p-2 bg-light rounded">
                            <strong>Observation :</strong> {{ $fidele->observation }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Associations --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-people me-2"></i>Associations / Fikambanana
                </div>
                <div class="card-body p-0">
                    @if($fidele->fikambananas->isEmpty())
                        <p class="text-muted p-3 mb-0">Aucune association.</p>
                    @else
                    <ul class="list-group list-group-flush">
                        @foreach($fidele->fikambananas as $fik)
                        <li class="list-group-item small">
                            <div class="fw-semibold">{{ $fik->libelle_fikambanana }}</div>
                            @if($fik->pivot->date_adhesion)
                                <small class="text-muted">Adhésion : {{ \Carbon\Carbon::parse($fik->pivot->date_adhesion)->format('d/m/Y') }}</small>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>

    </div>{{-- fin row --}}
</div>
@endsection