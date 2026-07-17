<?php // ===================================================================
// FICHIER : resources/views/finances/recap/evolution.blade.php
// =================================================================== ?>
{{-- ========== finances/recap/evolution.blade.php ========== --}}
@extends('layouts.app')
@section('title', 'Évolution du solde')
@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-graph-up-arrow text-success me-2"></i>Évolution du solde</h1>
        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="annee" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                @foreach($annees as $a)
                    <option value="{{ $a }}" {{ $a == $annee ? 'selected':'' }}>{{ $a }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Graphique principal --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">Recettes vs Dépenses — {{ $annee }}</div>
        <div class="card-body">
            <canvas id="chartRD" height="80"></canvas>
        </div>
    </div>

    {{-- Graphique solde cumulé --}}
    <div class="card shadow-sm">
        <div class="card-header">Solde cumulé — {{ $annee }}</div>
        <div class="card-body">
            <canvas id="chartSolde" height="60"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
const labels   = @json($labels);
const recettes = @json($dataRec);
const depenses = @json($dataDep);
const soldes   = @json($dataSol);

// Recettes vs Dépenses
new Chart(document.getElementById('chartRD'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Recettes',
                data: recettes,
                backgroundColor: 'rgba(25,135,84,0.7)',
                borderColor: 'rgba(25,135,84,1)',
                borderWidth: 1
            },
            {
                label: 'Dépenses',
                data: depenses,
                backgroundColor: 'rgba(220,53,69,0.7)',
                borderColor: 'rgba(220,53,69,1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: {
                ticks: {
                    callback: v => new Intl.NumberFormat('fr-MG').format(v) + ' Ar'
                }
            }
        }
    }
});

// Solde cumulé
new Chart(document.getElementById('chartSolde'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Solde cumulé',
            data: soldes,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,0.1)',
            fill: true,
            tension: 0.3,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                ticks: {
                    callback: v => new Intl.NumberFormat('fr-MG').format(v) + ' Ar'
                }
            }
        }
    }
});
</script>
@endpush
@endsection
