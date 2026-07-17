
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/pdf/recap_chapitre.blade.php      --}}
{{-- ============================================================ --}}
@extends('pdf.layout')
@php
    $moisLabel = \App\Models\Mois::find($mois)?->libelle_mois_fr ?? $mois;
    $titre = "Récap. par chapitre — {$moisLabel} {$annee}";
@endphp
 
@section('pdf-content')
 
<div class="doc-header">
    <h2>Récapitulatif par chapitre</h2>
    <p>{{ $moisLabel }} {{ $annee }}</p>
</div>
 
@php
    $totalRecettes = $donnees->filter(fn($d) => str_starts_with($d->chap_code, 'A'))->sum('total');
    $totalDepenses = $donnees->filter(fn($d) => str_starts_with($d->chap_code, 'B'))->sum('total');
    $solde         = $totalRecettes - $totalDepenses;
@endphp
 
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
        <div class="kpi-label">Résultat</div>
        <div class="kpi-value {{ $solde >= 0 ? 'text-success' : 'text-danger' }}">
            {{ ($solde >= 0 ? '+' : '') . number_format($solde, 0, ',', ' ') }} Ar
        </div>
    </div>
</div>
 
<table>
    <thead>
        <tr>
            <th style="width:80px;">Code</th>
            <th>Chapitre</th>
            <th style="width:80px;" class="text-center">Type</th>
            <th style="width:140px;" class="text-right">Montant (Ar)</th>
            <th style="width:60px;" class="text-right">%</th>
        </tr>
    </thead>
    <tbody>
        @foreach($donnees as $d)
            @php
                $estRecette = str_starts_with($d->chap_code, 'A');
                $totalRef   = $estRecette ? $totalRecettes : $totalDepenses;
                $pct        = $totalRef > 0 ? round($d->total / $totalRef * 100, 1) : 0;
            @endphp
            <tr>
                <td style="font-weight:bold;">{{ $d->chap_code }}</td>
                <td>{{ $d->chap_libelle }}</td>
                <td class="text-center">{{ $estRecette ? 'Recette' : 'Dépense' }}</td>
                <td class="text-right" style="font-weight:bold; color:{{ $estRecette ? '#198754' : '#dc3545' }};">
                    {{ number_format($d->total, 0, ',', ' ') }}
                </td>
                <td class="text-right">{{ $pct }}%</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total">
            <td colspan="3" class="text-right">Total Recettes</td>
            <td class="text-right">{{ number_format($totalRecettes, 0, ',', ' ') }} Ar</td>
            <td></td>
        </tr>
        <tr class="total">
            <td colspan="3" class="text-right">Total Dépenses</td>
            <td class="text-right">{{ number_format($totalDepenses, 0, ',', ' ') }} Ar</td>
            <td></td>
        </tr>
        <tr class="total">
            <td colspan="3" class="text-right">Résultat</td>
            <td class="text-right" style="color:{{ $solde >= 0 ? '#e8c46a' : '#ff6b6b' }};">
                {{ ($solde >= 0 ? '+' : '') . number_format($solde, 0, ',', ' ') }} Ar
            </td>
            <td></td>
        </tr>
    </tfoot>
</table>
 
@endsection
 