@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-geo-alt"></i> Emplacements</h3>
        <a href="{{ route('emplacements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouvel emplacement
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" class="mb-3">
        <div class="input-group" style="max-width: 400px;">
            <input type="text" name="search" class="form-control" placeholder="Rechercher un emplacement..."
                   value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                    <th>Toerana</th>
                    <th>Nom court</th>
                    <th class="text-center">Équipements</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($emplacements as $emplacement)
                    <tr>
                        <td>{{ $emplacement->idplace }}</td>
                        <td>{{ $emplacement->libelle_place }}</td>
                        <td>{{ $emplacement->toerana }}</td>
                        <td>{{ $emplacement->nom_court }}</td>
                        <td class="text-center">
                            <a href="{{ route('emplacements.show', $emplacement) }}" class="badge bg-info text-decoration-none">
                                {{ $emplacement->fitaovanas_count }}
                            </a>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('emplacements.edit', $emplacement) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('emplacements.destroy', $emplacement) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Supprimer cet emplacement ?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Aucun emplacement trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $emplacements->links() }}
</div>
@endsection
