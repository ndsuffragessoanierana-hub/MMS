<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
 
    <title>ECAR — @yield('title', 'Masina Maria Mpanampy')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @stack('style')
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1a2540;
            --sidebar-accent: #e8c46a;
            --topbar-h: 56px;
        }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }

        #sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--sidebar-bg);
            overflow-y: auto; z-index: 1040;
            transition: transform .25s ease;
        }
        #sidebar .brand { padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.1); }
        #sidebar .brand h6 { color: var(--sidebar-accent); font-weight: 700; margin: 0; letter-spacing:.5px; }
        #sidebar .brand small { color: rgba(255,255,255,.5); font-size: .72rem; }

        #sidebar .nav-section {
            padding: .5rem 1rem .25rem;
            font-size: .68rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: rgba(255,255,255,.35);
        }
        #sidebar .nav-link {
            color: rgba(255,255,255,.7);
            padding: .55rem 1.5rem;
            border-radius: 0;
            font-size: .875rem;
            display: flex; align-items: center; gap: .6rem;
            transition: all .15s;
        }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.08);
            border-left: 3px solid var(--sidebar-accent);
        }
        #sidebar .nav-link i { font-size: 1rem; width: 1.2rem; text-align:center; }

        #topbar {
            position: fixed; top: 0; left: var(--sidebar-width);
            right: 0; height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            z-index: 1030;
            display: flex; align-items: center;
            padding: 0 1.5rem;
        }
        #main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-h);
            min-height: calc(100vh - var(--topbar-h));
        }

        @media (max-width: 991px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #topbar, #main-content { left: 0; margin-left: 0; }
        }

        .stat-card { border: none; border-radius: .75rem; overflow: hidden; }
        .stat-card .icon-box {
            width: 52px; height: 52px;
            border-radius: .5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .hover-shadow:hover { box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1) !important; transition: box-shadow .2s; }

        .flash-success { border-left: 4px solid #198754; background: #d1e7dd; }
        .flash-error   { border-left: 4px solid #dc3545; background: #f8d7da; }
    </style>
</head>
<body>

<nav id="sidebar">
    <div class="brand">
        <div class="d-flex align-items-center gap-2 mb-1">
            
                <img src="{{ asset('images/ND.png') }}"
                    alt="ECAR Logo"
                    style="width:48px; height:48px; border-radius:8px; object-fit:contain;">
            <div>
                <h6>ECAR</h6>
                <small>Masina Maria Mpanampy</small>
            </div>
        </div>
    </div>

    <ul class="nav flex-column pt-2">

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active':'' }}"
               href="{{ route('dashboard') }}">
                <i class="bi bi-house-fill"></i> Accueil
            </a>
        </li>

        <li class="nav-section mt-2">Fidèles</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('fideles.*') ? 'active':'' }}"
               href="{{ route('fideles.index') }}">
                <i class="bi bi-people-fill"></i> Liber Status Animarum
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('faritraS.*') ? 'active':'' }}"
               href="{{ route('faritraS.index') }}">
                <i class="bi bi-map-fill"></i> Faritra (Secteurs)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('apvs.*') ? 'active':'' }}"
               href="{{ route('apvs.index') }}">
                <i class="bi bi-diagram-3-fill"></i> APV
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('fikambananas.*') ? 'active':'' }}"
               href="{{ route('fikambananas.index') }}">
                <i class="bi bi-person-lines-fill"></i> Fikambanana / Vaomiera
            </a>
        </li>

        <li class="nav-section mt-2">Finances</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('finances.journals.*') ? 'active':'' }}"
               href="{{ route('finances.journals.index') }}">
                <i class="bi bi-journal-text"></i> Livre Journal
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('finances.rubriques.*') ? 'active':'' }}"
               href="{{ route('finances.rubriques.index') }}">
                <i class="bi bi-tags-fill"></i> Rubriques
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('finances.chapitres.*') ? 'active':'' }}"
               href="{{ route('finances.chapitres.index') }}">
                <i class="bi bi-collection-fill"></i> Chapitres
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('finances.recap.*') ? 'active':'' }}"
               data-bs-toggle="collapse" href="#menuRecap">
                <i class="bi bi-bar-chart-fill"></i> Récapitulatifs
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse {{ request()->routeIs('finances.recap.*') ? 'show':'' }}" id="menuRecap">
                <ul class="nav flex-column ps-4">
                    <li><a class="nav-link py-1 small" href="{{ route('finances.recap.rubrique') }}">
                        <i class="bi bi-list-ul"></i> Par rubrique</a></li>
                    <li><a class="nav-link py-1 small" href="{{ route('finances.recap.chapitre') }}">
                        <i class="bi bi-pie-chart-fill"></i> Par chapitre</a></li>
                    <li><a class="nav-link py-1 small" href="{{ route('finances.recap.evolution') }}">
                        <i class="bi bi-graph-up-arrow"></i> Évolution solde</a></li>
                    <li><a class="nav-link py-1 small" href="{{ route('finances.recap.compte') }}">
                        <i class="bi bi-bank"></i> Journal par compte</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('finances.budget.*') ? 'active':'' }}"
               data-bs-toggle="collapse" href="#menuBudget">
                <i class="bi bi-clipboard2-data-fill"></i> Budget prévisionnel
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse {{ request()->routeIs('finances.budget.*') ? 'show':'' }}" id="menuBudget">
                <ul class="nav flex-column ps-4">
                    <li><a class="nav-link py-1 small" href="{{ route('finances.budget.annuel') }}">
                        <i class="bi bi-calendar-check"></i> Annuel</a></li>
                    <li><a class="nav-link py-1 small" href="{{ route('finances.budget.mensuel') }}">
                        <i class="bi bi-calendar3"></i> Mensuel</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-section mt-2">Inventaire</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('inventaire.*') ? 'active':'' }}"
               href="{{ route('inventaire.index') }}">
                <i class="bi bi-box-seam-fill"></i> Équipements
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('type-fitaovanas.*') ? 'active':'' }}"
               href="{{ route('type-fitaovanas.index') }}">
                <i class="bi bi-bookmarks-fill"></i> Types d'équipements
            </a>
        </li>

        <li class="nav-section mt-2">Pastoral</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('agenda.*') ? 'active':'' }}"
               href="{{ route('agenda.index') }}">
                <i class="bi bi-calendar-event-fill"></i> Agenda
            </a>
        </li>

        @auth
            <li class="nav-section mt-3">Compte</li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('profile.edit') }}">
                    {{ auth()->user()->name }}
                    <span class="badge bg-secondary ms-auto" style="font-size:.65rem;">
                        {{ auth()->user()->role }}
                    </span>
                </a>
            </li>
        @endauth
        <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent text-danger">
                    <i class="bi bi-box-arrow-left"></i> Déconnexion
                </button>
            </form>
        </li>

    </ul>
</nav>

<header id="topbar">
    <button class="btn btn-link text-dark d-lg-none me-2 p-0"
            onclick="document.getElementById('sidebar').classList.toggle('show')">
        <i class="bi bi-list fs-4"></i>
    </button>
    <span class="text-muted small">@yield('title', 'Accueil')</span>
    <div class="ms-auto d-flex align-items-center gap-3">
        {{--
        @php $soldeRapide = \App\Models\TJournal::orderByDesc('journal_id')->first(); @endphp 
        @if($soldeRapide) 
        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 font-monospace">
            <i class="bi bi-bank me-1"></i>
            {{ number_format($soldeRapide->solde_total, 0, ',', ' ') }} Ar
        </span>
        @endif
        --}}
        <span class="small text-muted d-none d-md-inline">{{ now()->locale('fr')->translatedFormat('l d F Y') }} — Paroisse ECAR Masina Maria Mpanampy</span>
    </div>
</header>

<main id="main-content">

    @if(session('success'))
    <div class="flash-success alert alert-dismissible fade show m-3 mb-0 py-2" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="flash-error alert alert-dismissible fade show m-3 mb-0 py-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show m-3 mb-0 py-2">
        <i class="bi bi-x-circle-fill me-2"></i>
        <strong>Erreur(s) de validation :</strong>
        <ul class="mb-0 mt-1 small">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@stack('scripts')
</body>
</html>
