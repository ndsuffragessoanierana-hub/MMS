<?php
// =============================================================================
// FICHIER : resources/views/fideles/_form.blade.php
// (Formulaire partagé create/edit)
// =============================================================================
?>
{{-- ========== _form.blade.php ========== --}}
<div class="row g-4">

    {{-- ── SECTION 1 : Identité ── --}}
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-person-badge me-2"></i>Identité
            </div>
            <div class="card-body row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold required">Matricule</label>
                    <input type="text" name="matricule"
                           value="{{ old('matricule', $fidele->matricule ?? $matricule ?? '') }}"
                           class="form-control @error('matricule') is-invalid @enderror"
                           {{ isset($fidele->matricule) && $fidele->exists ? 'readonly' : '' }}>
                    @error('matricule')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold required">Nom</label>
                    <input type="text" name="nom"
                           value="{{ old('nom', $fidele->nom) }}"
                           class="form-control @error('nom') is-invalid @enderror"
                           style="text-transform:uppercase">
                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold required">Prénom</label>
                    <input type="text" name="prenom"
                           value="{{ old('prenom', $fidele->prenom) }}"
                           class="form-control @error('prenom') is-invalid @enderror">
                    @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Nom de baptême</label>
                    <input type="text" name="nom_bapteme"
                           value="{{ old('nom_bapteme', $fidele->nom_bapteme) }}"
                           class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Sexe</label>
                    <select name="sexe" class="form-select @error('sexe') is-invalid @enderror">
                        <option value="">—</option>
                        <option value="M" {{ old('sexe', $fidele->sexe) == 'L' ? 'selected' : '' }}>Masculin</option>
                        <option value="F" {{ old('sexe', $fidele->sexe) == 'V' ? 'selected' : '' }}>Féminin</option>
                    </select>
                    @error('sexe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date de naissance</label>
                    <input type="date" name="date_naissance"
                           value="{{ old('date_naissance', $fidele->date_naissance?->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Lieu de naissance</label>
                    <input type="text" name="lieu_naissance"
                           value="{{ old('lieu_naissance', $fidele->lieu_naissance) }}"
                           class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Profession</label>
                    <input type="text" name="profession"
                           value="{{ old('profession', $fidele->profession) }}"
                           class="form-control">
                </div>
            </div>
        </div>
    </div>

    {{-- ── SECTION 2 : Famille ── --}}
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <i class="bi bi-house-heart me-2"></i>Famille
            </div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nom du père</label>
                    <input type="text" name="nom_pere"
                           value="{{ old('nom_pere', $fidele->nom_pere) }}"
                           class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nom de la mère</label>
                    <input type="text" name="nom_mere"
                           value="{{ old('nom_mere', $fidele->nom_mere) }}"
                           class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">N° Famille</label>
                    <input type="text" name="numero_famille"
                           value="{{ old('numero_famille', $fidele->numero_famille) }}"
                           class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">N° Registre</label>
                    <input type="text" name="numero_registre"
                           value="{{ old('numero_registre', $fidele->numero_registre) }}"
                           class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tuteur</label>
                    <input type="text" name="tuteur"
                           value="{{ old('tuteur', $fidele->tuteur) }}"
                           class="form-control">
                </div>
            </div>
        </div>
    </div>

    {{-- ── SECTION 3 : Sacrements ── --}}
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-stars me-2"></i>Sacrements
            </div>
            <div class="card-body row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date de baptême</label>
                    <input type="date" name="date_bapteme"
                           value="{{ old('date_bapteme', $fidele->date_bapteme?->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Lieu de baptême</label>
                    <input type="text" name="lieu_bapteme"
                           value="{{ old('lieu_bapteme', $fidele->lieu_bapteme) }}"
                           class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Prêtre (baptême)</label>
                    <input type="text" name="nom_pretre"
                           value="{{ old('nom_pretre', $fidele->nom_pretre) }}"
                           class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">1ère Confession</label>
                    <input type="date" name="date_confesse"
                           value="{{ old('date_confesse', $fidele->date_confesse?->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">1ère Communion</label>
                    <input type="date" name="date_communion"
                           value="{{ old('date_communion', $fidele->date_communion?->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Confirmation</label>
                    <input type="date" name="date_confirmation"
                           value="{{ old('date_confirmation', $fidele->date_confirmation?->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Mariage</label>
                    <input type="date" name="date_mariage"
                           value="{{ old('date_mariage', $fidele->date_mariage?->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Ordination</label>
                    <input type="date" name="date_ordination"
                           value="{{ old('date_ordination', $fidele->date_ordination?->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date de décès</label>
                    <input type="date" name="date_deces"
                           value="{{ old('date_deces', $fidele->date_deces?->format('Y-m-d')) }}"
                           class="form-control">
                </div>
            </div>
        </div>
    </div>

    {{-- ── SECTION 4 : Paroisse ── --}}
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <i class="bi bi-geo-alt me-2"></i>Rattachement paroissial
            </div>
            <div class="card-body row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Faritra (Secteur)</label>
                    <select name="idfaritra" class="form-select" id="form-faritra">
                        <option value="">— Choisir —</option>
                        @foreach($faritraS as $f)
                            <option value="{{ $f->idfaritra }}"
                                {{ old('idfaritra', $fidele->idfaritra) == $f->idfaritra ? 'selected' : '' }}>
                                {{ $f->libelle_faritra }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">APV</label>
                    <select name="idapv" class="form-select" id="form-apv">
                        <option value="">— Choisir —</option>
                        @foreach($apvs as $a)
                            <option value="{{ $a->idapv }}"
                                {{ old('idapv', $fidele->idapv) == $a->idapv ? 'selected' : '' }}>
                                {{ $a->libelle_apv }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date d'arrivée</label>
                    <input type="date" name="date_arrivee"
                           value="{{ old('date_arrivee', $fidele->date_arrivee?->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date d'intégration</label>
                    <input type="date" name="date_integration"
                           value="{{ old('date_integration', $fidele->date_integration?->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Adresse</label>
                    <textarea name="adresse" rows="2" class="form-control">{{ old('adresse', $fidele->adresse) }}</textarea>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="actif"  {{ old('statut', $fidele->statut) == 'actif'  ? 'selected':'' }}>Actif</option>
                        <option value="parti"  {{ old('statut', $fidele->statut) == 'parti'  ? 'selected':'' }}>Parti</option>
                        <option value="décédé" {{ old('statut', $fidele->statut) == 'décédé' ? 'selected':'' }}>Décédé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">A quitté ?</label>
                    <select name="quitte" class="form-select">
                        <option value="N" {{ old('quitte', $fidele->quitte ?? 'N') == 'N' ? 'selected':'' }}>Non</option>
                        <option value="O" {{ old('quitte', $fidele->quitte) == 'O' ? 'selected':'' }}>Oui</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Observation</label>
                    <textarea name="observation" rows="2" class="form-control">{{ old('observation', $fidele->observation) }}</textarea>
                </div>
            </div>
        </div>
    </div>

</div>{{-- fin row --}}

@push('scripts')
<script>
document.getElementById('form-faritra').addEventListener('change', function () {
    const idFaritra = this.value;
    const selApv    = document.getElementById('form-apv');
    selApv.innerHTML = '<option value="">— Chargement… —</option>';
    if (!idFaritra) { selApv.innerHTML = '<option value="">— Choisir —</option>'; return; }
    fetch(`/apv-par-faritra/${idFaritra}`)
        .then(r => r.json())
        .then(data => {
            selApv.innerHTML = '<option value="">— Choisir —</option>';
            data.forEach(a => {
                selApv.innerHTML += `<option value="${a.idapv}">${a.libelle_apv}</option>`;
            });
        });
});
</script>
@endpush