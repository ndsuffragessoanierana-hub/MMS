{{-- ============================================================ --}}
{{-- FICHIER : resources/views/faritra/index.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Faritra (Secteurs)')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-map-fill text-primary me-2"></i>Faritra (Secteurs)</h1>
            <small class="text-muted">{{ $faritras->count() }} secteur(s)</small>
        </div>
        @if(auth()->user()->peutAjouter())
        <a href="{{ route('faritraS.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nouveau Faritra
        </a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ordre</th>
                        <th>Sigle</th>
                        <th>Libellé</th>
                        <th>Saint Patron</th>
                        <th class="text-center">Fidèles</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faritras as $f)
                    <tr>
                        <td class="text-muted small">{{ $f->num_ordre_faritra ?? '—' }}</td>
                        <td><span class="badge bg-primary-subtle text-primary">{{ $f->sigle ?? '—' }}</span></td>
                        <td>
                            <a href="{{ route('faritraS.show', $f->idfaritra) }}" class="fw-semibold text-decoration-none">
                                {{ $f->libelle_faritra }}
                            </a>
                        </td>
                        <td class="small text-muted">{{ $f->st_patron ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info">{{ $f->fideles_count }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('faritraS.show', $f->idfaritra) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->peutModifier())
                                <a href="{{ route('faritraS.edit', $f->idfaritra) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @if(auth()->user()->peutSupprimer())
                                <form action="{{ route('faritraS.destroy', $f->idfaritra) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce Faritra ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun Faritra enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection