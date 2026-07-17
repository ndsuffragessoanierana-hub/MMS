<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Journal par compte</title>
    <style>
        @page { size: A4 landscape; margin: 100px 20px 60px 20px; }
        body   { font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        header { position: fixed; top: -80px; left: 0; right: 0; }
        footer { position: fixed; bottom: -40px; left: 0; right: 0; text-align: right; font-size: 8px; }
        .page-number:after { content: "Page " counter(page); }
        .title { font-size: 13px; font-weight: bold; }
        table  { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid black; padding: 3px 5px; font-size: 8px; }
        th     { background-color: #1a2540; color: #fff; text-align: center; }
        td.right  { text-align: right; }
        td.center { text-align: center; }
        .totaux td   { font-weight: bold; background-color: #e8c46a; color: #1a2540; }
        .section-recette td { background-color: #d4edda; color: #155724; font-weight: bold; }
        .section-depense  td { background-color: #f8d7da; color: #721c24; font-weight: bold; }
        .signature { margin-top: 40px; width: 100%; }
        .signature td { border: none; text-align: center; padding-top: 50px; }
    </style>
</head>
<body>

{{-- En-tête fixe --}}
<header>
    <table style="width:100%; border:none;">
        <tr>
            <td style="width:12%; text-align:left; border:none;">
                <img src="{{ public_path('images/ND.png') }}" alt="Logo" style="height:55px;">
            </td>
            <td style="width:76%; text-align:center; border:none;">
                <div class="title">ECAR MASINA MARIA MPANAMPY SOANIERANA</div>
                <div class="title">JOURNAL PAR COMPTE</div>
                <div style="font-size:9px;">
                    Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}
                    au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
                    @if($compteId)
                        &nbsp;|&nbsp; Compte : {{ $compteId }}
                        @if($libelleCompte ?? null) — {{ $libelleCompte }} @endif
                    @endif
                </div>
            </td>
            <td style="width:12%; border:none; text-align:right; font-size:8px; color:#555;">
                Édité le {{ now()->format('d/m/Y') }}<br>
                Par : {{ auth()->user()->name ?? '' }}
            </td>
        </tr>
    </table>
</header>

{{-- Pied de page --}}
<footer><div class="page-number"></div></footer>

{{-- KPI --}}
@php $solde = $totalRecettesGlobal - $totalDepensesGlobal; @endphp
<table style="width:60%; margin: 0 auto 12px auto;">
    <tr>
        <th>Total Recettes</th>
        <th>Total Dépenses</th>
        <th>Solde</th>
    </tr>
    <tr>
        <td class="right" style="color:#198754; font-weight:bold;">
            {{ number_format($totalRecettesGlobal, 2, ',', ' ') }} Ar
        </td>
        <td class="right" style="color:#dc3545; font-weight:bold;">
            {{ number_format($totalDepensesGlobal, 2, ',', ' ') }} Ar
        </td>
        <td class="right" style="font-weight:bold; color:{{ $solde >= 0 ? '#198754' : '#dc3545' }};">
            {{ ($solde >= 0 ? '+' : '') . number_format($solde, 2, ',', ' ') }} Ar
        </td>
    </tr>
</table>

{{-- Tableau détail --}}
<table>
    <thead>
        <tr>
            <th style="width:60px;">Date</th>
            <th>Libellé</th>
            <th style="width:55px;">Rubrique</th>
            <th style="width:55px;">Chapitre</th>
            <th style="width:45px;">Mode</th>
            <th style="width:100px;">Compte</th>
            <th style="width:90px;">Recette (Ar)</th>
            <th style="width:90px;">Dépense (Ar)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $currentChap = null;
            $totalRec    = 0;
            $totalDep    = 0;
        @endphp

        @foreach($details as $d)
            @php
                $estRecette  = str_starts_with($d->chap_code ?? '', 'A');
                $totalRec   += $estRecette ? $d->j_detail_montant : 0;
                $totalDep   += !$estRecette ? $d->j_detail_montant : 0;
            @endphp
            <tr>
                <td class="center">
                    {{ \Carbon\Carbon::parse($d->j_detail_date)->format('d/m/Y') }}
                </td>
                <td>{{ $d->j_detail_libelle }}</td>
                <td class="center">{{ $d->rub_rubrique_id }}</td>
                <td class="center">{{ $d->chap_code ?? '—' }}</td>
                <td class="center">{{ $d->j_detail_mode_paie ?? '—' }}</td>
                <td>
                    {{ $d->cpt_no_compte ?? '—' }}
                    @if($d->libelle_compte ?? null)
                        <br><span style="color:#555; font-size:7px;">{{ $d->libelle_compte }}</span>
                    @endif
                </td>
                <td class="right" style="{{ $estRecette ? 'color:#198754; font-weight:bold;' : '' }}">
                    {{ $estRecette ? number_format($d->j_detail_montant, 2, ',', ' ') : '' }}
                </td>
                <td class="right" style="{{ !$estRecette ? 'color:#dc3545; font-weight:bold;' : '' }}">
                    {{ !$estRecette ? number_format($d->j_detail_montant, 2, ',', ' ') : '' }}
                </td>
            </tr>
        @endforeach

        <tr class="totaux">
            <td colspan="6" class="right">TOTAL</td>
            <td class="right">{{ $totalRec ? number_format($totalRec, 2, ',', ' ') : '' }}</td>
            <td class="right">{{ $totalDep ? number_format($totalDep, 2, ',', ' ') : '' }}</td>
        </tr>
    </tbody>
</table>

<table class="signature">
    <tr>
        <td>Ampandalovina teo amin'ny Pretra</td>
        <td>Le Trésorier</td>
    </tr>
</table>

</body>
</html>
