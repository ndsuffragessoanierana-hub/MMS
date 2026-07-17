@extends('layouts.app')
@section('title', 'Chapitres comptables')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-collection-fill text-primary me-2"></i>Chapitres comptables</h1>
            <small class="text-muted">{{ $chapitres->count() }} chapitre(s)</small>
        </div>
        @if(auth()->user()->peutAjouter())
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAjout">
            <i class="bi bi-plus-circle me-1"></i>Nouveau chapitre
        </button>
        @endif
    </div>

    {{-- Liste des chapitres --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:120px;">Code</th>
                        <th>Libellé</th>
                        <th style="width:100px;" class="text-center">Type</th>
                        <th style="width:100px;" class="text-center">Rubriques</th>
                        <th style="width:80px;" class="text-center">Actif</th>
                        <th style="width:120px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chapitres as $ch)
                    @php $estRecette = str_starts_with($ch->chap_code, 'A'); @endphp
                    <tr>
                        <td>
                            <code class="fw-bold">{{ $ch->chap_code }}</code>
                        </td>
                        <td class="fw-semibold">{{ $ch->chap_libelle }}</td>
                        <td class="text-center">
                            @if($estRecette)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-arrow-down-circle me-1"></i>Recette
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-arrow-up-circle me-1"></i>Dépense
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary">
                                {{ $ch->rubriques_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($ch->actuel == 'O')
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="badge bg-secondary">Non</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                @if(auth()->user()->peutModifier())
                                <button class="btn btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ Str::slug($ch->chap_code) }}"
                                        title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif
                                @if(auth()->user()->peutSupprimer())
                                <form action="{{ route('finances.chapitres.destroy', $ch->chap_code) }}"
                                      method="POST"
                                      onsubmit="return confirm('Supprimer le chapitre {{ $ch->chap_code }} ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Supprimer"
                                            {{ $ch->rubriques_count > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Edit --}}
                    @if(auth()->user()->peutModifier())
                    <div class="modal fade" id="modalEdit{{ Str::slug($ch->chap_code) }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('finances.chapitres.update', $ch->chap_code) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Modifier — {{ $ch->chap_code }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Code</label>
                                            <input type="text" name="chap_code"
                                                   value="{{ $ch->chap_code }}"
                                                   class="form-control"
                                                   maxlength="20"
                                                   style="text-transform:uppercase"
                                                   {{ $ch->rubriques_count > 0 ? 'readonly' : '' }}>
                                            @if($ch->rubriques_count > 0)
                                                <div class="form-text text-warning">
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                    {{ $ch->rubriques_count }} rubrique(s) — code non modifiable
                                                </div>
                                            @else
                                                <div class="form-text text-muted">Modifiable si aucune rubrique</div>
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
                                            <input type="text" name="chap_libelle"
                                                   value="{{ $ch->chap_libelle }}"
                                                   class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Actif</label>
                                            <select name="actuel" class="form-select">
                                                <option value="O" {{ $ch->actuel == 'O' ? 'selected' : '' }}>Oui</option>
                                                <option value="N" {{ $ch->actuel == 'N' ? 'selected' : '' }}>Non</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                                data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="bi bi-check-lg me-1"></i>Enregistrer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-collection fs-2 d-block mb-2"></i>
                            Aucun chapitre enregistré.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Modal Ajout --}}
@if(auth()->user()->peutAjouter())
<div class="modal fade" id="modalAjout" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('finances.chapitres.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nouveau chapitre</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" name="chap_code"
                               value="{{ old('chap_code') }}"
                               class="form-control @error('chap_code') is-invalid @enderror"
                               placeholder="ex: A1"
                               style="text-transform:uppercase"
                               maxlength="20" required>
                        @error('chap_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text text-muted">
                            A… = Recette &nbsp;|&nbsp; B… = Dépense
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
                        <input type="text" name="chap_libelle"
                               value="{{ old('chap_libelle') }}"
                               class="form-control @error('chap_libelle') is-invalid @enderror"
                               placeholder="Libellé du chapitre" required>
                        @error('chap_libelle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Actif</label>
                        <select name="actuel" class="form-select">
                            <option value="O" selected>Oui</option>
                            <option value="N">Non</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check-lg me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.querySelector('input[name="chap_code"]')?.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalAjout')).show();
    });
@endif
</script>
@endpush
@endsection
