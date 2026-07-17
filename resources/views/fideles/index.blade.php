@extends('layouts.app')
@extends('layouts.app')

@section('title', 'Liber Status Animarum')

@section('content')
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="bi bi-people-fill me-2"></i>Liber Status Animarum
            </h1>
            <small class="text-muted">Registre des fidèles de la paroisse</small>
        </div>
        @if(auth()->user()->peutAjouter())
        <a href="{{ route('fideles.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i>Nouveau fidèle
        </a>
        @endif
    </div>

    {{-- Statistiques --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary">{{ number_format($stats['total']) }}</div>
                <div class="small text-muted">Total fidèles</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-info">{{ number_format($stats['hommes']) }}</div>
                <div class="small text-muted">Hommes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-pink">{{ number_format($stats['femmes']) }}</div>
                <div class="small text-muted">Femmes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-secondary">{{ number_format($stats['partis']) }}</div>
                <div class="small text-muted">Partis / Transférés</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('fideles.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Recherche</label>
                    <input type="text" name="recherche" value="{{ request('recherche') }}"
                           class="form-control form-control-sm"
                           placeholder="Nom, prénom, matricule…">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Faritra</label>
                    <select name="faritra_id" class="form-select form-select-sm" id="sel-faritra">
                        <option value="">— Tous —</option>
                        @foreach($faritraS as $f)
                            <option value="{{ $f->idfaritra }}"
                                {{ request('faritra_id') == $f->idfaritra ? 'selected' : '' }}>
                                {{ $f->libelle_faritra }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">APV</label>
                    <select name="apv_id" class="form-select form-select-sm" id="sel-apv">
                        <option value="">— Tous —</option>
                        @foreach($apvs as $a)
                            <option value="{{ $a->idapv }}"
                                {{ request('apv_id') == $a->idapv ? 'selected' : '' }}>
                                {{ $a->libelle_apv }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-semibold">Sexe</label>
                    <select name="sexe" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="M" {{ request('sexe') == 'M' ? 'selected' : '' }}>M</option>
                        <option value="F" {{ request('sexe') == 'F' ? 'selected' : '' }}>F</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Statut</label>
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="actif"   {{ request('statut')=='actif'   ? 'selected':'' }}>Actif</option>
                        <option value="parti"   {{ request('statut')=='parti'   ? 'selected':'' }}>Parti</option>
                        <option value="décédé"  {{ request('statut')=='décédé'  ? 'selected':'' }}>Décédé</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="bi bi-search me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('fideles.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table des fidèles --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Matricule</th>
                            <th>Nom & Prénom</th>
                            <th>Sexe</th>
                            <th>Né(e) le</th>
                            <th>Faritra</th>
                            <th>APV</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fideles as $f)
                        <tr>
                            <td><code class="small">{{ $f->matricule }}</code></td>
                            <td>
                                <a href="{{ route('fideles.show', $f->matricule) }}" class="fw-semibold text-decoration-none">
                                    {{ $f->nom_complet }}
                                </a>
                                @if($f->nom_bapteme)
                                    <br><small class="text-muted fst-italic">{{ $f->nom_bapteme }}</small>
                                @endif
                            </td>
                            <td>
                                @if($f->sexe == 'M')
                                    <span class="badge bg-info text-dark">M</span>
                                @elseif($f->sexe == 'F')
                                    <span class="badge bg-danger-subtle text-danger">F</span>
                                @endif
                            </td>
                            <td class="small">
                                {{ $f->date_naissance?->format('d/m/Y') ?? '—' }}
                                @if($f->age)
                                    <span class="text-muted">({{ $f->age }} ans)</span>
                                @endif
                            </td>
                            <td class="small">{{ $f->faritra?->sigle ?? $f->faritra?->libelle_faritra ?? '—' }}</td>
                            <td class="small">{{ $f->apv?->libelle_apv ?? '—' }}</td>
                            <td>
                                @if($f->date_deces)
                                    <span class="badge bg-secondary">Décédé</span>
                                @elseif($f->quitte == 'O')
                                    <span class="badge bg-warning text-dark">Parti</span>
                                @else
                                    <span class="badge bg-success">Actif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('fideles.show', $f->matricule) }}"
                                       class="btn btn-outline-primary" title="Voir la fiche">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->peutModifier())
                                    <a href="{{ route('fideles.edit', $f->matricule) }}"
                                       class="btn btn-outline-warning" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-search fs-3 d-block mb-2"></i>
                                Aucun fidèle trouvé avec ces critères.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                {{ $fideles->total() }} résultat(s) — Page {{ $fideles->currentPage() }} / {{ $fideles->lastPage() }}
            </small>
            {{ $fideles->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
// Chargement dynamique des APV selon Faritra
document.getElementById('sel-faritra').addEventListener('change', function () {
    const idFaritra = this.value;
    const selApv    = document.getElementById('sel-apv');
    selApv.innerHTML = '<option value="">— Chargement… —</option>';
    if (!idFaritra) {
        selApv.innerHTML = '<option value="">— Tous —</option>';
        return;
    }
    fetch(`/apv-par-faritra/${idFaritra}`)
        .then(r => r.json())
        .then(data => {
            selApv.innerHTML = '<option value="">— Tous —</option>';
            data.forEach(a => {
                selApv.innerHTML += `<option value="${a.idapv}">${a.libelle_apv}</option>`;
            });
        });
});
</script>
@endpush
@endsection