@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3><i class="bi bi-geo-alt"></i> Modifier l'emplacement</h3>

    <form action="{{ route('emplacements.update', $emplacement) }}" method="POST" class="mt-3" style="max-width: 600px;">
        @csrf
        @method('PUT')
        @include('emplacements._form')

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('emplacements.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
