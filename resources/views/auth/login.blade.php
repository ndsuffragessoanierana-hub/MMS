<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ECAR — Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 
    <style>
        :root {
            --primary: #1a2540;
            --accent:  #e8c46a;
        }
 
        * { box-sizing: border-box; margin: 0; padding: 0; }
 
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
 
        /* ── Cercles animés en arrière-plan ── */
        .bg-circles {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
        }
        .circle {
            position: absolute;
            border-radius: 50%;
            opacity: .08;
            animation: float 8s ease-in-out infinite;
        }
        .circle-1 {
            width: 400px; height: 400px;
            background: var(--accent);
            top: -100px; left: -100px;
            animation-delay: 0s;
        }
        .circle-2 {
            width: 300px; height: 300px;
            background: #4a90d9;
            bottom: -80px; right: -80px;
            animation-delay: 2s;
        }
        .circle-3 {
            width: 200px; height: 200px;
            background: var(--accent);
            top: 50%; right: 10%;
            animation-delay: 4s;
        }
        .circle-4 {
            width: 150px; height: 150px;
            background: #fff;
            bottom: 20%; left: 5%;
            animation-delay: 1s;
        }
 
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }
 
        /* ── Carte de connexion ── */
        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,.97);
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,.4);
            padding: 2.5rem 2rem;
            animation: slideUp .6s ease both;
        }
 
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }
 
        /* ── Logo ── */
        .logo-wrapper {
            text-align: center;
            margin-bottom: 1.5rem;
            animation: popIn .5s ease .2s both;
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(.7); }
            to   { opacity: 1; transform: scale(1); }
        }
 
        .logo-img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(26,37,64,.2);
            margin-bottom: .75rem;
            transition: transform .3s ease;
        }
        .logo-img:hover {
            transform: scale(1.08) rotate(-2deg);
        }
 
        .paroisse-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: .5px;
            line-height: 1.3;
        }
        .paroisse-sub {
            font-size: .78rem;
            color: #6c757d;
            margin-top: .2rem;
        }
 
        /* ── Séparateur doré ── */
        .divider {
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            border-radius: 2px;
            margin: 1.25rem 0;
            animation: expandWidth .8s ease .4s both;
        }
        @keyframes expandWidth {
            from { transform: scaleX(0); }
            to   { transform: scaleX(1); }
        }
 
        /* ── Titre connexion ── */
        .login-title {
            text-align: center;
            font-size: .9rem;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 1.5rem;
            animation: fadeIn .5s ease .5s both;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
 
        /* ── Champs formulaire ── */
        .form-group { margin-bottom: 1rem; animation: fadeIn .5s ease .6s both; }
        .form-group:nth-child(2) { animation-delay: .7s; }
 
        .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: .4rem;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: .65rem 1rem;
            font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(232,196,106,.2);
            outline: none;
        }
        .input-group .form-control { border-right: none; border-radius: 10px 0 0 10px; }
        .input-group .btn-eye {
            border: 2px solid #e9ecef;
            border-left: none;
            border-radius: 0 10px 10px 0;
            background: #f8f9fa;
            color: #6c757d;
            padding: 0 .75rem;
            cursor: pointer;
            transition: color .2s;
        }
        .input-group .btn-eye:hover { color: var(--primary); }
 
        /* ── Bouton connexion ── */
        .btn-login {
            width: 100%;
            padding: .75rem;
            background: linear-gradient(135deg, var(--primary), #2d3f6e);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            margin-top: .5rem;
            animation: fadeIn .5s ease .8s both;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26,37,64,.35);
        }
        .btn-login:active { transform: translateY(0); }
 
        /* ── Remember me ── */
        .remember-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: .75rem;
            animation: fadeIn .5s ease .75s both;
        }
        .remember-row input { accent-color: var(--accent); }
        .remember-row label { font-size: .82rem; color: #6c757d; cursor: pointer; }
 
        /* ── Erreurs ── */
        .alert-error {
            background: #fff3f3;
            border: 1px solid #f5c6cb;
            border-left: 4px solid #dc3545;
            border-radius: 10px;
            padding: .75rem 1rem;
            font-size: .82rem;
            color: #721c24;
            margin-bottom: 1rem;
            animation: shake .4s ease;
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            25%      { transform: translateX(-8px); }
            75%      { transform: translateX(8px); }
        }
 
        /* ── Footer carte ── */
        .card-footer-text {
            text-align: center;
            font-size: .72rem;
            color: #adb5bd;
            margin-top: 1.5rem;
            animation: fadeIn .5s ease 1s both;
        }
 
        /* ── Particules flottantes ── */
        .particles {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1;
            pointer-events: none;
        }
        .particle {
            position: absolute;
            width: 4px; height: 4px;
            background: var(--accent);
            border-radius: 50%;
            opacity: 0;
            animation: rise linear infinite;
        }
        @keyframes rise {
            0%   { opacity: 0;   transform: translateY(0) scale(0); }
            10%  { opacity: .6; }
            90%  { opacity: .2; }
            100% { opacity: 0;   transform: translateY(-100vh) scale(1); }
        }
    </style>
</head>
<body>
 
    {{-- Cercles d'arrière-plan --}}
    <div class="bg-circles">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
        <div class="circle circle-4"></div>
    </div>
 
    {{-- Particules --}}
    <div class="particles" id="particles"></div>
 
    {{-- Carte de connexion --}}
    <div class="login-card">
 
        {{-- Logo + nom paroisse --}}
        <div class="logo-wrapper">
            <img src="{{ asset('images/ND.png') }}" alt="Logo ECAR" class="logo-img">
            <div class="paroisse-name">ECAR Masina Maria Mpanampy</div>
            <div class="paroisse-sub">Antananarivo — Madagascar</div>
        </div>
 
        {{-- Séparateur doré --}}
        <div class="divider"></div>
 
        {{-- Titre --}}
        <div class="login-title">
            <i class="bi bi-shield-lock me-1"></i> Connexion
        </div>
 
        {{-- Erreurs --}}
        @if ($errors->any())
        <div class="alert-error">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
        @endif
 
        {{-- Formulaire --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf
 
            <div class="form-group">
                <label class="form-label" for="email">
                    <i class="bi bi-envelope me-1"></i>Adresse email
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-control"
                       placeholder="votre@email.com"
                       required autofocus autocomplete="email">
            </div>
 
            <div class="form-group">
                <label class="form-label" for="password">
                    <i class="bi bi-lock me-1"></i>Mot de passe
                </label>
                <div class="input-group">
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                    <button type="button" class="btn-eye" onclick="togglePassword()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
 
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Se souvenir de moi</label>
            </div>
 
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
            </button>
 
        </form>
 
        <div class="card-footer-text">
            <i class="bi bi-shield-check me-1"></i>
            Accès réservé aux membres autorisés
        </div>
 
    </div>
 
    <script>
    // ── Afficher/masquer mot de passe ──
    function togglePassword() {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            pwd.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
 
    // ── Génération des particules flottantes ──
    const container = document.getElementById('particles');
    for (let i = 0; i < 25; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.left     = Math.random() * 100 + 'vw';
        p.style.bottom   = '-10px';
        p.style.width    = (Math.random() * 4 + 2) + 'px';
        p.style.height   = p.style.width;
        p.style.animationDuration  = (Math.random() * 10 + 8) + 's';
        p.style.animationDelay     = (Math.random() * 10) + 's';
        p.style.opacity  = Math.random() * 0.5;
        container.appendChild(p);
    }
    </script>
 
</body>
</html>