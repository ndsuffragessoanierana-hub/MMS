@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-3">
    <label for="idplace" class="form-label">ID Place</label>
    <input type="text" name="idplace" id="idplace" class="form-control"
           value="{{ old('idplace', $emplacement->idplace ?? '') }}"
           {{ isset($emplacement) ? '' : 'required' }}>
    <small class="text-muted">Identifiant unique (ex: 1, 10, 21...)</small>
</div>

<div class="mb-3">
    <label for="libelle_place" class="form-label">Libellé</label>
    <input type="text" name="libelle_place" id="libelle_place" class="form-control"
           value="{{ old('libelle_place', $emplacement->libelle_place ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="toerana" class="form-label">Toerana</label>
    <input type="text" name="toerana" id="toerana" class="form-control"
           value="{{ old('toerana', $emplacement->toerana ?? '') }}">
</div>

<div class="mb-3">
    <label for="nom_court" class="form-label">Nom court</label>
    <input type="text" name="nom_court" id="nom_court" class="form-control"
           value="{{ old('nom_court', $emplacement->nom_court ?? '') }}">
</div>
