@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-geo-alt"></i> {{ $emplacement->libelle_place }}</h3>
        <a href="{{ route('emplacements.index') }}" class="btn btn-secondary btn-sm">Retour</a>
    </div>

    <dl class="row">
        <dt class="col-sm-2">ID Place</dt>
        <dd class="col-sm-10">{{ $emplacement->idplace }}</dd>
        <dt class="col-sm-2">Toerana</dt>
        <dd class="col-sm-10">{{ $emplacement->toerana }}</dd>
        <dt class="col-sm-2">Nom court</dt>
        <dd class="col-sm-10">{{ $emplacement->nom_court }}</dd>
    </dl>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h5 class="mt-4">Équipements à cet emplacement ({{ $emplacement->fitaovanas->count() }})</h5>
    <ul class="list-group mb-3">
        @forelse ($emplacement->fitaovanas as $fitaovana)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $fitaovana->denomination ?? $fitaovana->des_courte ?? $fitaovana->idfitaovana }}</strong>
                    @if ($fitaovana->no_inventaire)
                        <span class="text-muted small">— N° inv. {{ $fitaovana->no_inventaire }}</span>
                    @endif
                </div>
                <form action="{{ route('emplacements.equipements.remove', [$emplacement, $fitaovana->idfitaovana]) }}"
                      method="POST" onsubmit="return confirm('Retirer cet équipement de {{ $emplacement->libelle_place }} ?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i> Retirer</button>
                </form>
            </li>
        @empty
            <li class="list-group-item text-muted">Aucun équipement rattaché.</li>
        @endforelse
    </ul>

    <div class="card" style="max-width: 500px;">
        <div class="card-body">
            <h6 class="card-title">Ajouter / déplacer un équipement ici</h6>
            <form action="{{ route('emplacements.equipements.add', $emplacement) }}" method="POST" class="d-flex gap-2">
                @csrf
                <select name="fit_idfitaovana" class="form-select" required>
                    <option value="">-- Choisir un équipement --</option>
                    @foreach ($availableFitaovanas as $fitaovana)
                        <option value="{{ $fitaovana->idfitaovana }}">
                            {{ $fitaovana->denomination ?? $fitaovana->des_courte ?? $fitaovana->idfitaovana }}
                            @if ($fitaovana->currentEmplacementLabel)
                                (actuellement: {{ $fitaovana->currentEmplacementLabel }})
                            @endif
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-primary text-nowrap">Ajouter ici</button>
            </form>
            <small class="text-muted d-block mt-2">
                Si l'équipement est déjà ailleurs, il sera automatiquement retiré de son ancien emplacement.
            </small>
        </div>
    </div>
</div>
@endsection
