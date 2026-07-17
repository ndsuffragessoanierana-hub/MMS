
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/agenda/index.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Agenda')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-calendar-event-fill text-primary me-2"></i>Agenda</h1>
            <small class="text-muted">
                {{ \Carbon\Carbon::parse($dateDebut)->locale('fr')->translatedFormat('d F Y') }}
                →
                {{ \Carbon\Carbon::parse($dateFin)->locale('fr')->translatedFormat('d F Y') }}
                — {{ $evenements->count() }} événement(s)
            </small>
        </div>
        @if(auth()->user()->peutAjouter())
        <a href="{{ route('agenda.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nouvel événement
        </a>
        @endif
    </div>

    {{-- Filtres dates --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Date début</label>
                    <input type="date" name="date_debut" value="{{ $dateDebut }}"
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Date fin</label>
                    <input type="date" name="date_fin" value="{{ $dateFin }}"
                           class="form-control form-control-sm">
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i>Afficher
                    </button>
                    {{-- Raccourcis rapides --}}
                    <a href="{{ route('agenda.index', [
                            'date_debut' => now()->startOfMonth()->format('Y-m-d'),
                            'date_fin'   => now()->endOfMonth()->format('Y-m-d')
                        ]) }}" class="btn btn-outline-secondary btn-sm">
                        Ce mois
                    </a>
                    <a href="{{ route('agenda.index', [
                            'date_debut' => now()->format('Y-m-d'),
                            'date_fin'   => now()->addMonths(3)->format('Y-m-d')
                        ]) }}" class="btn btn-outline-secondary btn-sm">
                        3 mois
                    </a>
                    <a href="{{ route('agenda.index', [
                            'date_debut' => now()->startOfYear()->format('Y-m-d'),
                            'date_fin'   => now()->endOfYear()->format('Y-m-d')
                        ]) }}" class="btn btn-outline-secondary btn-sm">
                        Année {{ now()->year }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Liste des événements --}}
    <div class="card shadow-sm">
        @if($evenements->isEmpty())
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                Aucun événement sur cette période.
            </div>
        @else
        {{-- Groupement par mois --}}
        @php $moisCourant = null; @endphp
        <div class="list-group list-group-flush">
            @foreach($evenements as $ev)
                @php $mois = $ev->date_agenda->locale('fr')->translatedFormat('F Y'); @endphp

                {{-- Séparateur de mois --}}
                @if($mois !== $moisCourant)
                    @php $moisCourant = $mois; @endphp
                    <div class="list-group-item bg-light py-1">
                        <span class="fw-bold text-primary small text-uppercase">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ ucfirst($mois) }}
                        </span>
                    </div>
                @endif

                <div class="list-group-item py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex gap-3 align-items-start">
                            {{-- Badge date --}}
                            <div class="text-center flex-shrink-0" style="min-width:45px;">
                                <div class="fw-bold fs-5 text-primary lh-1">
                                    {{ $ev->date_agenda->format('d') }}
                                </div>
                                <div style="font-size:.65rem;" class="text-muted text-uppercase">
                                    {{ ucfirst($ev->date_agenda->locale('fr')->translatedFormat('D')) }}
                                </div>
                            </div>
                            {{-- Contenu --}}
                            <div>
                                <div class="fw-semibold">{{ $ev->libelle }}</div>
                                @if($ev->observation)
                                    <small class="text-muted">{{ $ev->observation }}</small>
                                @endif
                            </div>
                        </div>
                        {{-- Actions --}}
                        <div class="btn-group btn-group-sm ms-2 flex-shrink-0">
                            @if(auth()->user()->peutModifier())
                            <a href="{{ route('agenda.edit', $ev->id_agenda) }}"
                               class="btn btn-outline-secondary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                            @if(auth()->user()->peutSupprimer())
                            <form action="{{ route('agenda.destroy', $ev->id_agenda) }}" method="POST"
                                  onsubmit="return confirm('Supprimer cet événement ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection