
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/type-fitaovana/index.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', "Types d'équipements")
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-bookmarks-fill text-warning me-2"></i>Types d'équipements</h1>
        @if(auth()->user()->peutAjouter())
        <a href="{{ route('type-fitaovanas.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-circle me-1"></i>Nouveau type
        </a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Libellé</th>
                        <th class="text-center">Nb équipements</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $t)
                    <tr>
                        <td class="fw-semibold">{{ $t->libelle_type_fitaovana }}</td>
                        <td class="text-center"><span class="badge bg-warning-subtle text-warning">{{ $t->fitaovanas_count }}</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                @if(auth()->user()->peutModifier())
                                <a href="{{ route('type-fitaovanas.edit', $t->id_type_fitaovana) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @if(auth()->user()->peutSupprimer())
                                <form action="{{ route('type-fitaovanas.destroy', $t->id_type_fitaovana) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce type ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Aucun type enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
