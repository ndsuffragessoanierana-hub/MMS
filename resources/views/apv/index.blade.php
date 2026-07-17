
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/apv/index.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'APV')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-diagram-3-fill text-primary me-2"></i>APV</h1>
        @if(auth()->user()->peutAjouter())
        <a href="{{ route('apvs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nouvel APV
        </a>
        @endif
    </div>

    <form method="GET" class="d-flex gap-2 mb-3 align-items-end">
        <div>
            <label class="form-label small fw-semibold">Filtrer par Faritra</label>
            <select name="faritra_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">— Tous —</option>
                @foreach($faritras as $f)
                    <option value="{{ $f->idfaritra }}" {{ request('faritra_id') == $f->idfaritra ? 'selected':'' }}>
                        {{ $f->libelle_faritra }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>APV</th>
                        <th>Faritra</th>
                        <th class="text-center">Fidèles</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apvs as $a)
                    <tr>
                        <td>
                            <a href="{{ route('apvs.show', $a->idapv) }}" class="fw-semibold text-decoration-none">
                                {{ $a->libelle_apv }}
                            </a>
                        </td>
                        <td class="small text-muted">{{ $a->faritra?->libelle_faritra ?? '—' }}</td>
                        <td class="text-center"><span class="badge bg-info-subtle text-info">{{ $a->fideles_count }}</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('apvs.show', $a->idapv) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                @if(auth()->user()->peutModifier())
                                <a href="{{ route('apvs.edit', $a->idapv) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                @endif
                                @if(auth()->user()->peutSupprimer())
                                <form action="{{ route('apvs.destroy', $a->idapv) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cet APV ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Aucun APV enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
