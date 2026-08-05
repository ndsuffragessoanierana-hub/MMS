@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3><i class="bi bi-geo-alt"></i> Nouvel emplacement</h3>

    <form action="{{ route('emplacements.store') }}" method="POST" class="mt-3" style="max-width: 600px;">
        @csrf
        @include('emplacements._form')

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('emplacements.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
