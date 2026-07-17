
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/pdf/inventaire.blade.php          --}}
{{-- ============================================================ --}}
@extends('pdf.layout')
@php $titre = 'Liste des équipements — ' . now()->format('d/m/Y'); @endphp
 
@section('pdf-content')
 
<div class="doc-header">
    <h2>Inventaire des équipements</h2>
    <p>{{ $fitaovanas->count() }} équipement(s) — Valeur totale : {{ number_format($valeurTotale, 0, ',', ' ') }} Ar</p>
</div>
 
@foreach($fitaovanas->groupBy('type_libelle') as $typeLabel => $items)
<table>
    <thead>
        <tr>
            <th colspan="6" style="background:#e8c46a; color:#1a2540;">
                {{ $typeLabel ?: 'Sans type' }}
                ({{ $items->count() }})
            </th>
        </tr>
        <tr>
            <th style="width:80px;">N° Inventaire</th>
            <th>Dénomination</th>
            <th style="width:70px;">Référence</th>
            <th style="width:65px;">Date acq.</th>
            <th style="width:40px;" class="text-center">Qté</th>
            <th style="width:100px;" class="text-right">Valeur totale (Ar)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $f)
        <tr>
            <td>{{ $f->no_inventaire ?? '—' }}</td>
            <td>{{ $f->denomination }}</td>
            <td>{{ $f->reference ?? '—' }}</td>
            <td>{{ $f->date_acquisition ? \Carbon\Carbon::parse($f->date_acquisition)->format('d/m/Y') : '—' }}</td>
            <td class="text-center">{{ $f->qte_achetee }}</td>
            <td class="text-right" style="font-weight:bold;">
                {{ $f->valeur_acquisition ? number_format($f->valeur_acquisition * $f->qte_achetee, 0, ',', ' ') : '—' }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="sous-total">
            <td colspan="5" class="text-right">Sous-total</td>
            <td class="text-right">
                {{ number_format($items->sum(fn($i) => ($i->valeur_acquisition ?? 0) * $i->qte_achetee), 0, ',', ' ') }} Ar
            </td>
        </tr>
    </tfoot>
</table>
@endforeach
 
<table>
    <tfoot>
        <tr class="total">
            <td colspan="5" class="text-right">VALEUR TOTALE INVENTAIRE</td>
            <td class="text-right">{{ number_format($valeurTotale, 0, ',', ' ') }} Ar</td>
        </tr>
    </tfoot>
</table>
 
@endsection