@extends('layouts.app')
@section('title', 'Rubriques comptables')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-tags-fill text-primary me-2"></i>Rubriques comptables</h1>
            <small class="text-muted">{{ $rubriques->flatten()->count() }} rubrique(s)</small>
        </div>
        @if(auth()->user()->peutAjouter())
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAjout">
            <i class="bi bi-plus-circle me-1"></i>Nouvelle rubrique
        </button>
        @endif
    </div>

    {{-- Rubriques groupées par chapitre --}}
    @forelse($rubriques as $chapCode => $rubs)
        @php
            $chapitre   = $rubs->first()->chapitre;
            $estRecette = str_starts_with($chapCode, 'A');
        @endphp
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold
                {{ $estRecette ? 'bg-success text-white' : 'bg-danger text-white' }}">
                <i class="bi bi-{{ $estRecette ? 'arrow-down-circle' : 'arrow-up-circle' }} me-2"></i>
                {{ $chapCode }} — {{ $chapitre?->chap_libelle ?? 'Sans chapitre' }}
                <span class="badge bg-white text-{{ $estRecette ? 'success' : 'danger' }} ms-2">
                    {{ $rubs->count() }}
                </span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:120px;">Code</th>
                            <th>Libellé</th>
                            <th style="width:120px;">Date saisie</th>
                            <th style="width:120px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rubs as $r)
                        <tr>
                            <td><code>{{ $r->rubrique_id }}</code></td>
                            <td>{{ $r->rubrique_libelle }}</td>
                            <td class="small text-muted">
                                {{ $r->date_saisie?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    @if(auth()->user()->peutModifier())
                                    <button class="btn btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit{{ $r->rubrique_id }}"
                                            title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @endif
                                    @if(auth()->user()->peutSupprimer())
                                    <form action="{{ route('finances.rubriques.destroy', $r->rubrique_id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Supprimer la rubrique {{ $r->rubrique_id }} ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Edit inline --}}
                        @if(auth()->user()->peutModifier())
                        <div class="modal fade" id="modalEdit{{ $r->rubrique_id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('finances.rubriques.update', $r->rubrique_id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Modifier — {{ $r->rubrique_id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Code</label>
                                                <input type="text" name="rubrique_id"
                                                    value="{{ $r->rubrique_id }}"
                                                    class="form-control"
                                                    maxlength="20"
                                                    style="text-transform:uppercase"
                                                    {{ $r->detailJournals()->exists() ? 'readonly' : '' }}>
                                                @if($r->detailJournals()->exists())
                                                    <div class="form-text text-warning">
                                                        <i class="bi bi-exclamation-triangle"></i> Code non modifiable (écritures existantes)
                                                    </div>
                                                @else
                                                    <div class="form-text text-muted">Modifiable si aucune écriture</div>
                                                @endif
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Libellé</label>
                                                <input type="text" name="rubrique_libelle"
                                                       value="{{ $r->rubrique_libelle }}"
                                                       class="form-control" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Chapitre</label>
                                                <select name="chap_code" class="form-select" required>
                                                    @foreach($chapitres as $ch)
                                                        <option value="{{ $ch->chap_code }}"
                                                            {{ $ch->chap_code == $r->chap_code ? 'selected' : '' }}>
                                                            {{ $ch->chap_code }} — {{ $ch->chap_libelle }}
                                                        </option>
                                                    @endforeach
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

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card shadow-sm">
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-tags fs-2 d-block mb-2"></i>
                Aucune rubrique enregistrée.
            </div>
        </div>
    @endforelse

</div>

{{-- Modal Ajout --}}
@if(auth()->user()->peutAjouter())
<div class="modal fade" id="modalAjout" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('finances.rubriques.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nouvelle rubrique</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" name="rubrique_id"
                               value="{{ old('rubrique_id') }}"
                               class="form-control @error('rubrique_id') is-invalid @enderror"
                               placeholder="ex: A01"
                               style="text-transform:uppercase"
                               maxlength="20" required>
                        @error('rubrique_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
                        <input type="text" name="rubrique_libelle"
                               value="{{ old('rubrique_libelle') }}"
                               class="form-control @error('rubrique_libelle') is-invalid @enderror"
                               placeholder="Libellé de la rubrique"
                               required>
                        @error('rubrique_libelle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Chapitre <span class="text-danger">*</span></label>
                        <select name="chap_code"
                                class="form-select @error('chap_code') is-invalid @enderror"
                                required>
                            <option value="">— Choisir un chapitre —</option>
                            @foreach($chapitres as $ch)
                                <option value="{{ $ch->chap_code }}"
                                    {{ old('chap_code') == $ch->chap_code ? 'selected' : '' }}>
                                    {{ $ch->chap_code }} — {{ $ch->chap_libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('chap_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
// Forcer majuscules sur le code rubrique
document.querySelector('input[name="rubrique_id"]')?.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Rouvrir le modal si erreur de validation
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalAjout')).show();
    });
@endif
</script>
@endpush
@endsection
