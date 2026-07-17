@extends('layouts.app')
@section('title', $inventaire->denomination)
@section('content')
<div class="container-fluid py-4">
 
    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('inventaire.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0">{{ $inventaire->denomination }}</h1>
                @if($inventaire->no_inventaire)
                    <small class="text-muted">
                        <i class="bi bi-hash"></i> N° {{ $inventaire->no_inventaire }}
                    </small>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->peutModifier())
            <a href="{{ route('inventaire.edit', $inventaire->idfitaovana) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>
            @endif
            @if(auth()->user()->peutSupprimer())
            <form action="{{ route('inventaire.destroy', $inventaire->idfitaovana) }}" method="POST"
                  onsubmit="return confirm('Supprimer cet équipement ?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>Supprimer
                </button>
            </form>
            @endif
        </div>
    </div>
 
    <div class="row g-4">
 
        {{-- Informations principales --}}
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark fw-semibold">
                    <i class="bi bi-box-seam me-2"></i>Informations générales
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Dénomination</dt>
                        <dd class="col-sm-8">{{ $inventaire->denomination }}</dd>
 
                        <dt class="col-sm-4">N° Inventaire</dt>
                        <dd class="col-sm-8">
                            <code>{{ $inventaire->no_inventaire ?? '—' }}</code>
                        </dd>
 
                        <dt class="col-sm-4">Référence</dt>
                        <dd class="col-sm-8">{{ $inventaire->reference ?? '—' }}</dd>
 
                        <dt class="col-sm-4">Type</dt>
                        <dd class="col-sm-8">
                            @if($inventaire->typeFitaovana)
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                    {{ $inventaire->typeFitaovana->libelle_type_fitaovana }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>
 
                        <dt class="col-sm-4">Fournisseur</dt>
                        <dd class="col-sm-8">{{ $inventaire->fournisseur ?? '—' }}</dd>
 
                        <dt class="col-sm-4">Date d'acquisition</dt>
                        <dd class="col-sm-8">
                            {{ $inventaire->date_acquisition?->format('d/m/Y') ?? '—' }}
                        </dd>
 
                        <dt class="col-sm-4">Quantité</dt>
                        <dd class="col-sm-8">{{ $inventaire->qte_achetee }}</dd>
 
                        <dt class="col-sm-4">Valeur unitaire</dt>
                        <dd class="col-sm-8 font-monospace">
                            {{ $inventaire->valeur_acquisition
                                ? number_format($inventaire->valeur_acquisition, 0, ',', ' ') . ' Ar'
                                : '—' }}
                        </dd>
 
                        <dt class="col-sm-4">Valeur totale</dt>
                        <dd class="col-sm-8">
                            <span class="fw-bold text-success font-monospace">
                                {{ $inventaire->valeur_acquisition
                                    ? number_format($inventaire->valeur_totale, 0, ',', ' ') . ' Ar'
                                    : '—' }}
                            </span>
                        </dd>
 
                        @if($inventaire->remarque)
                        <dt class="col-sm-4">Remarque</dt>
                        <dd class="col-sm-8">
                            <div class="p-2 bg-light rounded small">{{ $inventaire->remarque }}</div>
                        </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
 
        {{-- QR Code + Dates --}}
        <div class="col-md-4">
 
            {{-- Dates système --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header fw-semibold bg-light">
                    <i class="bi bi-clock-history me-2"></i>Suivi
                </div>
                <div class="card-body small">
                    <dl class="row mb-0">
                        <dt class="col-6 text-muted">Créé le</dt>
                        <dd class="col-6">{{ $inventaire->created_at?->format('d/m/Y') ?? '—' }}</dd>
                        <dt class="col-6 text-muted">Modifié le</dt>
                        <dd class="col-6">{{ $inventaire->updated_at?->format('d/m/Y') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
 
            {{-- QR Code (données texte) --}}
            @if($inventaire->qr_text)
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-light">
                    <i class="bi bi-qr-code me-2"></i>Données QR Code
                </div>
                <div class="card-body">
                    @php
                        $qrData = json_decode($inventaire->qr_text, true) ?? [];
                    @endphp
                    <dl class="row mb-0 small">
                        @foreach($qrData as $key => $val)
                        <dt class="col-4 text-muted text-capitalize">{{ $key }}</dt>
                        <dd class="col-8">{{ $val ?: '—' }}</dd>
                        @endforeach
                    </dl>
                    <div class="mt-2 p-2 bg-light rounded font-monospace small text-muted"
                         style="word-break:break-all; font-size:.7rem;">
                        {{ $inventaire->qr_text }}
                    </div>
                </div>
            </div>
            @endif
 
        </div>
 
    </div>
 
    {{-- Bouton retour bas de page --}}
    <div class="mt-4">
        <a href="{{ route('inventaire.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Retour à la liste
        </a>
    </div>
 
</div>
@endsection