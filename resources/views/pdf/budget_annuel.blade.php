<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color:#222; }
    h1 { text-align:center; font-size:16px; margin-bottom:2px; }
    .sub { text-align:center; margin-bottom:18px; color:#555; }
    table { width:100%; border-collapse:collapse; margin-bottom:14px; }
    th, td { border:1px solid #999; padding:4px 6px; }
    th { background:#eee; }
    .text-end { text-align:right; }
    .chap-header { background:#333; color:#fff; font-weight:bold; }
    .totaux td { font-weight:bold; background:#f5f5f5; }
</style>
</head>
<body>
    <h1>Budget prévisionnel annuel</h1>
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
                    <th colspan="3">{{ $chapCode }} — {{ $chapitre?->chap_libelle }}</th>
                </tr>
                <tr>
                    <th style="width:80px;">Code</th>
                    <th>Rubrique</th>
                    <th style="width:120px;" class="text-end">Montant (Ar)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rubs as $r)
                    <tr>
                        <td>{{ $r->rubrique_id }}</td>
                        <td>{{ $r->rubrique_libelle }}</td>
                        <td class="text-end">
                            {{ number_format($r->ligneBudgets->first()?->lg_bdg_montant ?? 0, 0, ',', ' ') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <table class="totaux">
        <tr>
            <td>Total Recettes prévues</td>
            <td class="text-end">{{ number_format($totalRecettes, 0, ',', ' ') }} Ar</td>
        </tr>
        <tr>
            <td>Total Dépenses prévues</td>
            <td class="text-end">{{ number_format($totalDepenses, 0, ',', ' ') }} Ar</td>
        </tr>
        <tr>
            <td>Solde prévisionnel</td>
            <td class="text-end">{{ number_format($totalRecettes - $totalDepenses, 0, ',', ' ') }} Ar</td>
        </tr>
    </table>
</body>
</html>