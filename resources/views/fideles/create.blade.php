<?php
// =============================================================================
// FICHIER : resources/views/fideles/create.blade.php
// =============================================================================
?>
{{-- ========== create.blade.php ========== --}}
@extends('layouts.app')
@section('title', 'Nouveau fidèle')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('fideles.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="h4 mb-0"><i class="bi bi-person-plus text-primary me-2"></i>Nouveau fidèle</h1>
    </div>

    <form action="{{ route('fideles.store') }}" method="POST">
        @csrf
        @include('fideles._form')
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('fideles.index') }}" class="btn btn-outline-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection