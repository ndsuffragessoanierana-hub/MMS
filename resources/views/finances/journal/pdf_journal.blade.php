<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Livre Journal</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 100px 20px 60px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            text-align: center;
            line-height: 1.5;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 10px;
        }

        .page-number:after {
            content: "Page " counter(page);
        }

        .title {
            font-size: 14px;
            font-weight: bold;
        }

        .box {
            border: 1px solid black;
            padding: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 3px 5px;
        }

        th {
            background-color: #1a2540;
            color: #ffffff;
            text-align: center;
            font-size: 9px;
        }

        td { font-size: 9px; }

        td.right { text-align: right; }
        td.center { text-align: center; }

        .totaux td {
            font-weight: bold;
            background-color: #e8c46a;
            color: #1a2540;
        }

        .section-recette td { background-color: #d4edda; color: #155724; font-weight: bold; }
        .section-depense  td { background-color: #f8d7da; color: #721c24; font-weight: bold; }

        .signature {
            margin-top: 40px;
            width: 100%;
        }
        .signature td {
            border: none;
            text-align: center;
            padding-top: 50px;
            font-size: 10px;
        }
    </style>
</head>
<body>

{{-- ── En-tête fixe ── --}}
<header>
    <table style="width:100%; border:none;">
        <tr>
            <td style="width:15%; text-align:left; border:none;">
                <img src="{{ public_path('images/ND.png') }}" alt="Logo" style="height:60px;">
            </td>
            <td style="width:70%; text-align:center; border:none;">
                <div class="title">ECAR MASINA MARIA MPANAMPY SOANIERANA</div>
                <div class="title">LIVRE JOURNAL — {{ strtoupper($periode) }}</div>
            </td>
            <td style="width:15%; border:none; text-align:right; font-size:9px; color:#555;">
                Édité le {{ now()->format('d/m/Y') }}<br>
                Par : {{ auth()->user()->name ?? '' }}
            </td>
        </tr>
    </table>
</header>

{{-- ── Pied de page fixe ── --}}
<footer>
    <div class="page-number"></div>
</footer>


{{-- ═══════════════════════════════════════ --}}
{{-- PAGE 1 : RÉCAPITULATIF                 --}}
{{-- ═══════════════════════════════════════ --}}

<h3 style="text-align:center; font-size:12px; margin-bottom:8px;">
    RÉCAPITULATIF DES RECETTES ET DÉPENSES
</h3>

<table>
    <thead>
        <tr>
            <th style="width:80px;">Code</th>
            <th>Rubrique</th>
            <th style="width:120px;">Recette (Ar)</th>
            <th style="width:120px;">Dépense (Ar)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalRecapRecette = 0;
            $totalRecapDepense = 0;
            $currentChap       = null;
        @endphp

        @foreach($recap as $r)
            {{-- Séparateur chapitre --}}
            @if($r->chap_code !== $currentChap)
                @php $currentChap = $r->chap_code; @endphp
                <tr class="{{ str_starts_with($r->chap_code, 'A') ? 'section-recette' : 'section-depense' }}">
                    <td colspan="4">
                        {{ $r->chap_code }} — {{ $r->chap_libelle }}
                    </td>
                </tr>
            @endif

            <tr>
                <td>{{ $r->rubrique_id }}</td>
                <td>{{ $r->rubrique_libelle }}</td>
                <td class="right">
                    {{ $r->recette ? number_format($r->recette, 2, ',', ' ') : '' }}
                </td>
                <td class="right">
                    {{ $r->depense ? number_format($r->depense, 2, ',', ' ') : '' }}
                </td>
            </tr>

            @php
                $totalRecapRecette += $r->recette ?? 0;
                $totalRecapDepense += $r->depense ?? 0;
            @endphp
        @endforeach

        <tr class="totaux">
            <td colspan="2" class="right">TOTAL</td>
            <td class="right">{{ $totalRecapRecette ? number_format($totalRecapRecette, 2, ',', ' ') : '' }}</td>
            <td class="right">{{ $totalRecapDepense ? number_format($totalRecapDepense, 2, ',', ' ') : '' }}</td>
        </tr>
    </tbody>
</table>

<br><br>

{{-- ── Solde précédent / Solde courant ── --}}
<table style="width:100%; border:none;">
    <tr>
        {{-- Solde précédent --}}
        <td style="width:48%; vertical-align:top; border:none; padding-right:10px;">
            <table>
                <tbody>
                    <tr><th colspan="2">Solde précédent</th></tr>
                    <tr>
                        <td>BNI</td>
                        <td class="right">{{ $soldePrecedent->journal_solde_bni ? number_format($soldePrecedent->journal_solde_bni, 2, ',', ' ') : '—' }}</td>
                    </tr>
                    <tr>
                        <td>BRED</td>
                        <td class="right">{{ $soldePrecedent->journal_solde_bfv ? number_format($soldePrecedent->journal_solde_bfv, 2, ',', ' ') : '—' }}</td>
                    </tr>
                    <tr>
                        <td>Caisse</td>
                        <td class="right">{{ $soldePrecedent->journal_solde_caisse ? number_format($soldePrecedent->journal_solde_caisse, 2, ',', ' ') : '—' }}</td>
                    </tr>
                    <tr class="totaux">
                        <td>Total</td>
                        <td class="right">{{ $soldePrecedent->total ? number_format($soldePrecedent->total, 2, ',', ' ') : '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </td>

        {{-- Solde courant --}}
        <td style="width:48%; vertical-align:top; border:none; padding-left:10px;">
            <table>
                <tbody>
                    <tr><th colspan="2">Solde à la fin du mois — {{ $periode }}</th></tr>
                    <tr>
                        <td>BNI</td>
                        <td class="right">{{ $soldeCourant->journal_solde_bni ? number_format($soldeCourant->journal_solde_bni, 2, ',', ' ') : '—' }}</td>
                    </tr>
                    <tr>
                        <td>BRED</td>
                        <td class="right">{{ $soldeCourant->journal_solde_bfv ? number_format($soldeCourant->journal_solde_bfv, 2, ',', ' ') : '—' }}</td>
                    </tr>
                    <tr>
                        <td>Caisse</td>
                        <td class="right">{{ $soldeCourant->journal_solde_caisse ? number_format($soldeCourant->journal_solde_caisse, 2, ',', ' ') : '—' }}</td>
                    </tr>
                    <tr class="totaux">
                        <td>Total</td>
                        <td class="right">{{ $soldeCourant->total ? number_format($soldeCourant->total, 2, ',', ' ') : '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>

<br>

{{-- ── Résultat du mois ── --}}
@php
    $resultat = $totalRecapRecette - $totalRecapDepense;
@endphp
<table style="width:50%; margin:0 auto;">
    <tbody>
        <tr>
            <th colspan="2">Résultat du mois</th>
        </tr>
        <tr>
            <td>Total Recettes</td>
            <td class="right" style="color:#198754; font-weight:bold;">
                {{ number_format($totalRecapRecette, 2, ',', ' ') }} Ar
            </td>
        </tr>
        <tr>
            <td>Total Dépenses</td>
            <td class="right" style="color:#dc3545; font-weight:bold;">
                {{ number_format($totalRecapDepense, 2, ',', ' ') }} Ar
            </td>
        </tr>
        <tr class="totaux">
            <td>Résultat</td>
            <td class="right" style="color:{{ $resultat >= 0 ? '#155724' : '#721c24' }};">
                {{ ($resultat >= 0 ? '+' : '') . number_format($resultat, 2, ',', ' ') }} Ar
            </td>
        </tr>
    </tbody>
</table>

{{-- ── Signatures ── --}}
<table class="signature">
    <tr>
        <td>Ampandalovina teo amin'ny Pretra</td>
        <td>Le Trésorier</td>
    </tr>
</table>


{{-- ═══════════════════════════════════════ --}}
{{-- SAUT DE PAGE → DÉTAIL                  --}}
{{-- ═══════════════════════════════════════ --}}
<div style="page-break-before: always;"></div>

<h3 style="text-align:center; font-size:12px; margin-bottom:8px;">
    DÉTAIL DES ÉCRITURES — {{ strtoupper($periode) }}
</h3>

<div class="box">
    <table>
        <thead>
            <tr>
                <th style="width:30px;">N°</th>
                <th style="width:60px;">Date</th>
                <th>Libellé</th>
                <th style="width:55px;">Rubrique</th>
                <th style="width:90px;">Recette (Ar)</th>
                <th style="width:90px;">Dépense (Ar)</th>
                <th style="width:80px;">Espèces R</th>
                <th style="width:80px;">Espèces D</th>
                <th style="width:80px;">BRED R</th>
                <th style="width:80px;">BRED D</th>
                <th style="width:80px;">BNI R</th>
                <th style="width:80px;">BNI D</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totals = [
                    'recette_g'  => 0,
                    'depense_g'  => 0,
                    'recette_num'=> 0,
                    'depense_num'=> 0,
                    'recette_bfv'=> 0,
                    'depense_bfv'=> 0,
                    'recette_bni'=> 0,
                    'depense_bni'=> 0,
                ];
                $currentChap = null;
            @endphp

            @foreach($details as $d)
                {{-- Séparateur chapitre --}}
                @if(($d->chap_code ?? null) !== $currentChap)
                    @php $currentChap = $d->chap_code ?? null; @endphp
                    @if($currentChap)
                    <tr class="{{ str_starts_with($currentChap, 'A') ? 'section-recette' : 'section-depense' }}">
                        <td colspan="12">
                            {{ $currentChap }} — {{ $d->chap_libelle ?? '' }}
                        </td>
                    </tr>
                    @endif
                @endif

                <tr>
                    <td class="center">{{ $d->j_detail_numero }}</td>
                    <td class="center">
                        {{ \Carbon\Carbon::parse($d->j_detail_date)->format('d/m/Y') }}
                    </td>
                    <td>{{ $d->j_detail_libelle }}</td>
                    <td class="center">{{ $d->rub_rubrique_id }}</td>
                    <td class="right">{{ $d->recette_g  ? number_format($d->recette_g,  2, ',', ' ') : '' }}</td>
                    <td class="right">{{ $d->depense_g  ? number_format($d->depense_g,  2, ',', ' ') : '' }}</td>
                    <td class="right">{{ $d->recette_num? number_format($d->recette_num, 2, ',', ' ') : '' }}</td>
                    <td class="right">{{ $d->depense_num? number_format($d->depense_num, 2, ',', ' ') : '' }}</td>
                    <td class="right">{{ $d->recette_bfv? number_format($d->recette_bfv, 2, ',', ' ') : '' }}</td>
                    <td class="right">{{ $d->depense_bfv? number_format($d->depense_bfv, 2, ',', ' ') : '' }}</td>
                    <td class="right">{{ $d->recette_bni? number_format($d->recette_bni, 2, ',', ' ') : '' }}</td>
                    <td class="right">{{ $d->depense_bni? number_format($d->depense_bni, 2, ',', ' ') : '' }}</td>
                </tr>

                @php
                    foreach($totals as $key => $val) {
                        $totals[$key] += $d->$key ?? 0;
                    }
                @endphp
            @endforeach

            <tr class="totaux">
                <td colspan="4" class="right">TOTAL</td>
                @foreach($totals as $val)
                    <td class="right">{{ $val ? number_format($val, 2, ',', ' ') : '' }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>
