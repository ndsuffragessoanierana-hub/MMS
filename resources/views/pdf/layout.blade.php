
{{-- ============================================================ --}}
{{-- FICHIER : resources/views/pdf/layout.blade.php             --}}
{{-- Layout commun pour tous les PDFs ECAR                      --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $titre ?? 'ECAR' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
 
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            color: #1a1a1a;
            background: #fff;
        }
 
        /* ── En-tête paroisse ── */
        .header {
            border-bottom: 2px solid #1a2540;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-inner {
            display: table;
            width: 100%;
        }
        .header-logo {
            display: table-cell;
            width: 60px;
            vertical-align: middle;
        }
        .header-logo img {
            width: 50px;
            height: 50px;
        }
        .header-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 10px;
        }
        .header-info h1 {
            font-size: 13pt;
            font-weight: bold;
            color: #1a2540;
        }
        .header-info p {
            font-size: 8pt;
            color: #555;
            margin-top: 2px;
        }
        .header-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            font-size: 8pt;
            color: #555;
        }
        .header-right .doc-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1a2540;
        }
 
        /* ── Titre du document ── */
        .doc-header {
            background: #1a2540;
            color: #fff;
            padding: 6px 10px;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        .doc-header h2 {
            font-size: 11pt;
            font-weight: bold;
        }
        .doc-header p {
            font-size: 8pt;
            opacity: .8;
            margin-top: 2px;
        }
 
        /* ── Tableaux ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background: #1a2540;
            color: #fff;
            padding: 5px 6px;
            text-align: left;
            font-size: 8pt;
            font-weight: bold;
        }
        th.text-right { text-align: right; }
        th.text-center { text-align: center; }
        td {
            padding: 4px 6px;
            font-size: 8pt;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }
        td.text-right { text-align: right; }
        td.text-center { text-align: center; }
        tr:nth-child(even) { background: #f8f9fa; }
 
        /* Séparateur de section (chapitre) */
        tr.section-header td {
            background: #e8c46a;
            color: #1a2540;
            font-weight: bold;
            font-size: 8pt;
            padding: 4px 6px;
        }
        tr.section-recette td { background: #d4edda; color: #155724; font-weight: bold; }
        tr.section-depense  td { background: #f8d7da; color: #721c24; font-weight: bold; }
 
        /* Totaux */
        tr.total td {
            background: #1a2540;
            color: #fff;
            font-weight: bold;
            border-top: 2px solid #e8c46a;
        }
        tr.sous-total td {
            background: #dee2e6;
            font-weight: bold;
        }
 
        /* ── KPI cards ── */
        .kpi-row {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        .kpi-cell {
            display: table-cell;
            width: 33%;
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            text-align: center;
        }
        .kpi-cell + .kpi-cell { margin-left: 8px; }
        .kpi-label { font-size: 7pt; color: #6c757d; }
        .kpi-value { font-size: 12pt; font-weight: bold; }
        .text-success { color: #198754; }
        .text-danger  { color: #dc3545; }
        .text-primary { color: #0d6efd; }
 
        /* ── Pied de page ── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #dee2e6;
            padding: 4px 0;
            font-size: 7pt;
            color: #6c757d;
            text-align: center;
        }
        .page-number:before { content: "Page " counter(page); }
        .page-total:after   { content: " / " counter(pages); }
 
        /* Marges du contenu pour éviter le footer */
        .content { margin-bottom: 30px; }
    </style>
</head>
<body>
 
{{-- En-tête --}}
<div class="header">
    <div class="header-inner">
        <div class="header-logo">
            <img src="{{ public_path('images/ND.png') }}" alt="Logo">
        </div>
        <div class="header-info">
            <h1>ECAR Masina Maria Mpanampy</h1>
            <p>Antananarivo — Madagascar</p>
            <p>Paroisse Notre-Dame</p>
        </div>
        <div class="header-right">
            <div class="doc-title">{{ $titre ?? '' }}</div>
            <div>Édité le {{ now()->locale('fr')->translatedFormat('d F Y') }}</div>
            <div>Par : {{ auth()->user()->name ?? '' }}</div>
        </div>
    </div>
</div>
 
{{-- Pied de page --}}
<div class="footer">
    ECAR Masina Maria Mpanampy &nbsp;|&nbsp; {{ $titre ?? '' }} &nbsp;|&nbsp;
    <span class="page-number"></span><span class="page-total"></span>
</div>
 
{{-- Contenu --}}
<div class="content">
    @yield('pdf-content')
</div>
 
</body>
</html>