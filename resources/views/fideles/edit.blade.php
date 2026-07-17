<?php
// =============================================================================
// FICHIER : resources/views/fideles/edit.blade.php
// =============================================================================
?>
{{-- ========== edit.blade.php ========== --}}
@extends('layouts.app')
@section('title', 'Modifier — ' . $fidele->nom_complet)
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('fideles.show', $fidele->matricule) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="h4 mb-0">
            <i class="bi bi-pencil-square text-warning me-2"></i>
            Modifier : {{ $fidele->nom_complet }}
        </h1>
        <code class="small text-muted">{{ $fidele->matricule }}</code>
    </div>

    <form action="{{ route('fideles.update', $fidele->matricule) }}" method="POST">
        @csrf
        @method('PUT')
        @include('fideles._form')
        <div class="d-flex justify-content-between mt-4">
            @if(auth()->user()->peutSupprimer())
            <form action="{{ route('fideles.destroy', $fidele->matricule) }}" method="POST"
                  onsubmit="return confirm('Marquer ce fidèle comme parti ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-person-dash me-1"></i>Marquer comme parti
                </button>
            </form>
            @endif
            <div class="d-flex gap-2">
                <a href="{{ route('fideles.show', $fidele->matricule) }}" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-lg me-1"></i>Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>
@endsection