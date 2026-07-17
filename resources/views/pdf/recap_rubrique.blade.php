
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/pdf/recap_rubrique.blade.php       --}}
{{-- ============================================================ --}}
@extends('pdf.layout')
@php
    $moisLabel = \App\Models\Mois::find($mois)?->libelle_mois_fr ?? $mois;
    $titre = "Récap. par rubrique — {$moisLabel} {$annee}";
@endphp
 
@section('pdf-content')
 
<div class="doc-header">
    <h2>Récapitulatif par rubrique</h2>
    <p>{{ $moisLabel }} {{ $annee }}</p>
</div>
 
{{-- KPI --}}
<div class="kpi-row">
    <div class="kpi-cell">
        <div class="kpi-label">Total Recettes</div>
        <div class="kpi-value text-success">{{ number_format($totalRecettes, 0, ',', ' ') }} Ar</div>
    </div>
    <div class="kpi-cell" style="margin:0 6px;">
        <div class="kpi-label">Total Dépenses</div>
        <div class="kpi-value text-danger">{{ number_format($totalDepenses, 0, ',', ' ') }} Ar</div>
    </div>
    <div class="kpi-cell">
        @php $solde = $totalRecettes - $totalDepenses; @endphp
        <div class="kpi-label">Résultat</div>
        <div class="kpi-value {{ $solde >= 0 ? 'text-success' : 'text-danger' }}">
            {{ ($solde >= 0 ? '+' : '') . number_format($solde, 0, ',', ' ') }} Ar
        </div>
    </div>
</div>
 
@foreach($rubriques as $chapCode => $rubs)
    @php
        $estRecette  = str_starts_with($chapCode, 'A');
        $chapLibelle = $rubs->first()->chap_libelle ?? $chapCode;
        $totalChap   = $rubs->sum(fn($r) => $donnees[$r->rubrique_id]->total ?? 0);
    @endphp
    <table>
        <thead>
            <tr>
                <th colspan="3" style="background:{{ $estRecette ? '#198754' : '#dc3545' }};">
                    {{ $chapCode }} — {{ $chapLibelle }}
                    &nbsp;|&nbsp; {{ number_format($totalChap, 0, ',', ' ') }} Ar
                </th>
            </tr>
            <tr>
                <th style="width:70px;">Code</th>
                <th>Libellé</th>
                <th style="width:120px;" class="text-right">Montant (Ar)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rubs as $r)
                @php $total = $donnees[$r->rubrique_id]->total ?? 0; @endphp
                <tr>
                    <td>{{ $r->rubrique_id }}</td>
                    <td>{{ $r->rubrique_libelle }}</td>
                    <td class="text-right" style="font-weight:bold; color:{{ $estRecette ? '#198754' : '#dc3545' }};">
                        {{ number_format($total, 0, ',', ' ') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="sous-total">
                <td colspan="2" class="text-right">Sous-total</td>
                <td class="text-right">{{ number_format($totalChap, 0, ',', ' ') }} Ar</td>
            </tr>
        </tfoot>
    </table>
@endforeach
 
@endsection
 