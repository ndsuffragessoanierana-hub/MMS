
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/fikambanana/show.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', $fikambanana->libelle_fikambanana)
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('fikambananas.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="h3 mb-0">{{ $fikambanana->libelle_fikambanana }}</h1>
                @if($fikambanana->st_patron)
                    <small class="text-muted"><i class="bi bi-star-fill text-warning"></i> {{ $fikambanana->st_patron }}</small>
                @endif
            </div>
        </div>
        @if(auth()->user()->peutModifier())
        <a href="{{ route('fikambananas.edit', $fikambanana->idfikambanana) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        @endif
    </div>

    <div class="row g-4">
        {{-- Membres actuels --}}
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Membres ({{ $fikambanana->fideles->count() }})
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Rôle</th>
                                <th>Adhésion</th>
                                @if(auth()->user()->peutSupprimer())<th></th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fikambanana->fideles as $f)
                            <tr>
                                <td>
                                    <a href="{{ route('fideles.show', $f->matricule) }}" class="text-decoration-none">
                                        {{ $f->nom_complet }}
                                    </a>
                                </td>
                                <td class="small text-muted">
                                    {{ \App\Models\MembreRole::find($f->pivot->code)?->libelle ?? '—' }}
                                </td>
                                <td class="small">
                                    {{ $f->pivot->date_adhesion ? \Carbon\Carbon::parse($f->pivot->date_adhesion)->format('d/m/Y') : '—' }}
                                </td>
                                @if(auth()->user()->peutSupprimer())
                                <td>
                                    <form action="{{ route('fikambananas.membres.retirer', [$fikambanana->idfikambanana, $f->matricule]) }}"
                                          method="POST" onsubmit="return confirm('Retirer ce membre ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-x"></i></button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Aucun membre.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Ajouter un membre --}}
        @if(auth()->user()->peutAjouter())
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Ajouter un membre</div>
                <div class="card-body">
                    <form action="{{ route('fikambananas.membres.ajouter', $fikambanana->idfikambanana) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Fidèle</label>
                            <select name="matricule" class="form-select form-select-sm" required>
                                <option value="">— Choisir —</option>
                                @foreach($membresDisponibles as $m)
                                    <option value="{{ $m->matricule }}">{{ $m->nom_complet }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Rôle</label>
                            <select name="code" class="form-select form-select-sm">
                                <option value="">— Aucun —</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->code }}">{{ $r->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Date d'adhésion</label>
                            <input type="date" name="date_adhesion" value="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm">
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-plus-lg me-1"></i>Ajouter
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
