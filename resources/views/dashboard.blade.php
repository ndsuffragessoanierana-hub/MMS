@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('content')
<div class="container-fluid py-4">

    {{-- ══ En-tête ══ --}}
    <div class="mb-4">
        <h1 class="h3 mb-0">Bonjour, {{ auth()->user()->name }} 👋</h1>
        {{--
        <small class="text-muted">
            {{ ucfirst(\Carbon\Carbon::now()->locale('fr')->translatedFormat('l d F Y')) }}
            — Paroisse ECAR Masina Maria Mpanampy
        </small>
        --}}
    </div>

    {{-- ══ Ligne 1 : KPI Finances ══ --}}
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
                        <i class="bi bi-graph-{{ $res >= 0 ? 'up' : 'down' }}-arrow text-{{ $res >= 0 ? 'success' : 'danger' }}"></i>
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

    {{-- ══ Ligne 2 : KPI Fidèles + Inventaire ══ --}}
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
                    <div class="text-muted small mb-2">Répartition H/F</div>
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

    {{-- ══ Ligne 3 : Graphiques ══ --}}
    <div class="row g-4 mb-4 align-items-stretch">

        {{-- Graphique Recettes/Dépenses --}}
        <div class="col-lg-7 d-flex flex-column">
            <div class="card shadow-sm flex-grow-1">
                <div class="card-header fw-semibold">
                    <i class="bi bi-bar-chart me-2 text-success"></i>
                    Vola Niditra sy Nivoaka — {{ $anneeEnCours }}
                </div>
                <div class="card-body d-flex align-items-center">
                    <canvas id="chartDashboard"></canvas>
                </div>
            </div>
        </div>

        {{-- Répartition Faritra --}}
        <div class="col-lg-5 d-flex flex-column">
            <div class="card shadow-sm flex-grow-1">
                <div class="card-header fw-semibold">
                    <i class="bi bi-people-fill me-2 text-info"></i>
                    Fitsinjaran'ny Kristianina isaky ny Faritra
                </div>
                <div class="card-body overflow-auto" style="max-height:320px;">
                    @php
                        $totalFideles = $repartFaritra->sum('nb');
                        $palette = ['#0d6efd','#198754','#dc3545','#ffc107','#0dcaf0',
                                    '#6610f2','#d63384','#fd7e14','#20c997','#6f42c1',
                                    '#495057','#adb5bd','#e83e8c','#17a2b8','#28a745'];
                    @endphp

                    <div class="text-center mb-2">
                        <span class="fs-5 fw-bold text-primary">{{ number_format($totalFideles) }}</span>
                        <span class="text-muted small ms-1">fidèles actifs</span>
                    </div>

                    <div class="row g-1">
                        @foreach($repartFaritra as $i => $f)
                            @php
                                $pct   = $totalFideles > 0 ? round($f->nb / $totalFideles * 100, 1) : 0;
                                $color = $palette[$i % count($palette)];
                            @endphp
                            <div class="col-6">
                                <div class="px-2 py-1 rounded-1" style="background:#f8f9fa; border-left:3px solid {{ $color }};">
                                    <div style="font-size:.7rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                                        title="{{ $f->libelle_faritra }}">
                                        {{ $f->libelle_faritra }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="font-size:.75rem; font-weight:700; color:{{ $color }};">
                                            {{ number_format($f->nb) }}
                                        </span>
                                        <span style="font-size:.6rem; color:#6c757d;">{{ $pct }}%</span>
                                    </div>
                                    <div style="height:3px; background:#dee2e6; border-radius:2px; margin-top:2px;">
                                        <div style="width:{{ $pct }}%; height:3px; background:{{ $color }}; border-radius:2px;"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══ Ligne 4 : Agenda + Accès rapides ══ --}}
    <div class="row g-4">

        {{-- Agenda --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold"><i class="bi bi-calendar-event me-2 text-primary"></i>Prochains événements</span>
                    <a href="{{ route('agenda.index') }}" class="btn btn-link btn-sm p-0 small">Voir tout</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($prochainsEvents as $ev)
                    <div class="list-group-item py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="small fw-semibold">{{ $ev->libelle }}</span>
                            <span class="badge bg-primary-subtle text-primary ms-2 flex-shrink-0" style="font-size:.7rem;">
                                {{ $ev->date_agenda->format('d/m') }}
                            </span>
                        </div>
                        @if($ev->observation)
                        <small class="text-muted">{{ Str::limit($ev->observation, 60) }}</small>
                        @endif
                    </div>
                    @empty
                    <div class="list-group-item text-muted small text-center py-3">
                        <i class="bi bi-calendar-x d-block mb-1 fs-4"></i>
                        Aucun événement à venir.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Accès rapides --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">
                    <i class="bi bi-lightning-fill text-warning me-2"></i>Accès rapides
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach([
                            ['route' => 'fideles.create',           'icon' => 'person-plus-fill',    'label' => 'Nouveau fidèle',     'color' => 'primary'],
                            ['route' => 'finances.journals.create', 'icon' => 'journal-plus',         'label' => 'Ouvrir un journal',  'color' => 'success'],
                            ['route' => 'inventaire.create',        'icon' => 'box-seam',             'label' => 'Ajouter équipement', 'color' => 'warning'],
                            ['route' => 'finances.recap.evolution', 'icon' => 'graph-up-arrow',       'label' => 'Évolution solde',    'color' => 'info'],
                            ['route' => 'finances.recap.rubrique',  'icon' => 'list-columns-reverse', 'label' => 'Récap. rubrique',    'color' => 'secondary'],
                            ['route' => 'agenda.create',            'icon' => 'calendar-plus',        'label' => 'Ajouter événement',  'color' => 'danger'],
                        ] as $lien)
                        <div class="col-6 col-md-4">
                            <a href="{{ route($lien['route']) }}"
                               class="btn btn-outline-{{ $lien['color'] }} w-100 text-start d-flex align-items-center gap-2 py-3">
                                <i class="bi bi-{{ $lien['icon'] }} fs-5"></i>
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
window.addEventListener('load', function () {
    const labels = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
    const chartRec = @json($chartRec);
    const chartDep = @json($chartDep);

    new Chart(document.getElementById('chartDashboard'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Recettes',
                    data: chartRec,
                    backgroundColor: 'rgba(25,135,84,.7)',
                    borderColor: 'rgba(25,135,84,1)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Dépenses',
                    data: chartDep,
                    backgroundColor: 'rgba(220,53,69,.7)',
                    borderColor: 'rgba(220,53,69,1)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: {
                    ticks: {
                        callback: function(v) {
                            return new Intl.NumberFormat('fr').format(v) + ' Ar';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
