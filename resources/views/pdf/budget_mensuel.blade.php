<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color:#222; }
    h1 { text-align:center; font-size:16px; margin-bottom:2px; }
    .sub { text-align:center; margin-bottom:18px; color:#555; }
    table { width:100%; border-collapse:collapse; margin-bottom:12px; }
    th, td { border:1px solid #999; padding:3px 4px; }
    th { background:#eee; }
    .text-end { text-align:right; }
    .chap-header { background:#333; color:#fff; font-weight:bold; }
</style>
</head>
<body>
    <h1>Budget prévisionnel mensuel</h1>
    <div class="sub">
        Exercice {{ $exercice->label ?? '' }}
        @if($exercice?->date_debut)
            — {{ $exercice->date_debut->format('d/m/Y') }} → {{ $exercice->date_fin->format('d/m/Y') }}
        @endif
    </div>

    @foreach($rubriques->groupBy('chap_code') as $chapCode => $rubs)
        @php $chapitre = $rubs->first()->chapitre; @endphp
        <table>
            <thead>
                <tr class="chap-header">
                    <th colspan="{{ $moisListe->count() + 2 }}">{{ $chapCode }} — {{ $chapitre?->chap_libelle }}</th>
                </tr>
                <tr>
                    <th style="width:140px;">Rubrique</th>
                    @foreach($moisListe as $m)
                        <th class="text-end">{{ substr($m->libelle_mois_fr, 0, 3) }}</th>
                    @endforeach
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rubs as $r)
                    @php
                        $budgetsMois   = $r->ligneBudgetMensuels->keyBy('mois');
                        $totalRubrique = 0;
                    @endphp
                    <tr>
                        <td>{{ $r->rubrique_id }} — {{ $r->rubrique_libelle }}</td>
                        @foreach($moisListe as $m)
                            @php
                                $montant = $budgetsMois[$m->numero]?->lg_bdg_montant ?? 0;
                                $totalRubrique += $montant;
                            @endphp
                            <td class="text-end">{{ $montant ? number_format($montant, 0, ',', ' ') : '' }}</td>
                        @endforeach
                        <td class="text-end"><strong>{{ number_format($totalRubrique, 0, ',', ' ') }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>