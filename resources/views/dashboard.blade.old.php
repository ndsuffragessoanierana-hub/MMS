@extends('layouts.app')
@section('title', 'Tableau de bord')
@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <h1 class="h3 mb-0">Bonjour, {{ auth()->user()->name }} 👋</h1>
        <small class="text-muted">{{ now()->translatedFormat('l d F Y') }} — Paroisse ECAR Masina Maria Mpanampy</small>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-success bg-opacity-10">
                        <i class="bi bi-arrow-down-circle text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Recettes {{ $anneeEnCours }}</div>
                        <div class="fw-bold font-monospace">
                            {{ number_format($recettesAnnee, 0, ',', ' ') }}<span class="small fw-normal"> Ar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-danger bg-opacity-10">
                        <i class="bi bi-arrow-up-circle text-danger"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Dépenses {{ $anneeEnCours }}</div>
                        <div class="fw-bold font-monospace">
                            {{ number_format($depensesAnnee, 0, ',', ' ') }}<span class="small fw-normal"> Ar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            @php $res = $recettesAnnee - $depensesAnnee; @endphp
            <div class="card stat-card shadow-sm border-{{ $res >= 0 ? 'success' : 'danger' }} border-2">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-{{ $res >= 0 ? 'success' : 'danger' }} bg-opacity-10">
                        <i class="bi bi-graph-{{ $res >= 0 ? 'up' : 'down' }}-arrow
                           text-{{ $res >= 0 ? 'success' : 'danger' }}"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Résultat {{ $anneeEnCours }}</div>
                        <div class="fw-bold font-monospace text-{{ $res >= 0 ? 'success' : 'danger' }}">
                            {{ ($res >= 0 ? '+' : '') . number_format($res, 0, ',', ' ') }}<span class="small fw-normal"> Ar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary bg-opacity-10">
                        <i class="bi bi-bank text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted small">
                            Solde total
                            @if($dernierJournal)
                                <span class="badge bg-secondary-subtle text-secondary" style="font-size:.65rem;">
                                    {{ $dernierJournal->periode }}
                                </span>
                            @endif
                        </div>
                        <div class="fw-bold font-monospace">
                            {{ number_format($dernierJournal?->solde_total ?? 0, 0, ',', ' ') }}<span class="small fw-normal"> Ar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-info bg-opacity-10">
                        <i class="bi bi-people-fill text-info"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Fidèles actifs</div>
                        <div class="fw-bold fs-4">{{ number_format($statsFideles['total']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small mb-1">Répartition H/F</div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-info text-dark">H {{ $statsFideles['hommes'] }}</span>
                        <div class="progress flex-grow-1" style="height:6px">
                            @php $pctH = $statsFideles['total'] > 0 ? ($statsFideles['hommes']/$statsFideles['total']*100) : 50; @endphp
                            <div class="progress-bar bg-info" style="width:{{ $pctH }}%"></div>
                        </div>
                        <span class="badge bg-danger-subtle text-danger">F {{ $statsFideles['femmes'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-warning bg-opacity-10">
                        <i class="bi bi-box-seam text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Équipements</div>
                        <div class="fw-bold fs-4">{{ $nbEquipements }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-secondary bg-opacity-10">
                        <i class="bi bi-currency-dollar text-secondary"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Valeur inventaire</div>
                        <div class="fw-bold font-monospace small">
                            {{ number_format($valeurInventaire, 0, ',', ' ') }} Ar
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-bar-chart me-2 text-success"></i>
                    Vola Niditra sy Nivoaka — {{ $anneeEnCours }}
                </div>
                <div class="card-body">
                    <canvas id="chartDashboard" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-pie-chart me-2 text-info"></i>
                    Fitsinjaran'ny Kristianina isaky ny Faritra
                </div>
                <div class="card-body">
                    {{-- Conteneur avec hauteur fixe explicite --}}
                    <div style="height:250px; position:relative;">
                        <canvas id="chartFaritra" style="height:250px !important;"></canvas>
                    </div>
                    <div class="mt-3">
                        @foreach($repartFaritra->take(13) as $f)
                        <div class="d-flex justify-content-between small py-1 border-bottom">
                            <span>{{ $f->libelle_faritra }}</span>
                            <span class="fw-semibold">{{ $f->nb }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between">
                    <span class="fw-semibold"><i class="bi bi-calendar-event me-2 text-primary"></i>Prochains événements</span>
                    <a href="{{ route('agenda.index') }}" class="btn btn-link btn-sm p-0">Voir tout</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($prochainsEvents as $ev)
                    <div class="list-group-item py-2">
                        <div class="d-flex justify-content-between">
                            <span class="small fw-semibold">{{ $ev->libelle }}</span>
                            <span class="badge bg-primary-subtle text-primary" style="font-size:.7rem;">
                                {{ $ev->date_agenda->format('d/m') }}
                            </span>
                        </div>
                        @if($ev->observation)
                        <small class="text-muted">{{ Str::limit($ev->observation, 60) }}</small>
                        @endif
                    </div>
                    @empty
                    <div class="list-group-item text-muted small text-center py-3">
                        Aucun événement à venir.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">
                    <i class="bi bi-lightning-fill text-warning me-2"></i>Accès rapides
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach([
                            ['route' => 'fideles.create',           'icon' => 'person-plus-fill',   'label' => 'Nouveau fidèle',     'color' => 'primary'],
                            ['route' => 'finances.journals.create', 'icon' => 'journal-plus',        'label' => 'Ouvrir un journal',  'color' => 'success'],
                            ['route' => 'inventaire.create',        'icon' => 'box-seam',            'label' => 'Ajouter équipement', 'color' => 'warning'],
                            ['route' => 'finances.recap.evolution', 'icon' => 'graph-up-arrow',      'label' => 'Évolution solde',    'color' => 'info'],
                            ['route' => 'finances.recap.rubrique',  'icon' => 'list-columns-reverse','label' => 'Récap. rubrique',    'color' => 'secondary'],
                            ['route' => 'agenda.create',            'icon' => 'calendar-plus',       'label' => 'Ajouter événement',  'color' => 'danger'],
                        ] as $lien)
                        <div class="col-6 col-md-4">
                            <a href="{{ route($lien['route']) }}"
                               class="btn btn-outline-{{ $lien['color'] }} w-100 text-start d-flex align-items-center gap-2">
                                <i class="bi bi-{{ $lien['icon'] }}"></i>
                                <span class="small">{{ $lien['label'] }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// ── Barres Recettes/Dépenses ──
new Chart(document.getElementById('chartDashboard'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [
            { label: 'Recettes', data: @json($chartRec), backgroundColor: 'rgba(25,135,84,.7)', borderRadius: 4 },
            { label: 'Dépenses', data: @json($chartDep), backgroundColor: 'rgba(220,53,69,.7)',  borderRadius: 4 }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { ticks: { callback: v => new Intl.NumberFormat('fr').format(v) + ' Ar' } } }
    }
});

// ── Camembert Faritra ──
document.addEventListener('DOMContentLoaded', function() {
    // ── Camembert Faritra ──
    const ctxFaritra = document.getElementById('chartFaritra').getContext('2d');
    new Chart(ctxFaritra, {
        type: 'pie',
        data: {
            labels: @json($repartFaritra->take(13)->pluck('libelle_faritra')),
            datasets: [{
                data: @json($repartFaritra->take(13)->pluck('nb')),
                backgroundColor: [
                    '#0d6efd','#198754','#dc3545','#ffc107','#0dcaf0',
                    '#6610f2','#d63384','#fd7e14','#20c997','#6f42c1',
                    '#495057','#adb5bd','#e83e8c'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,  // ← clé du problème
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, boxWidth: 12 }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
