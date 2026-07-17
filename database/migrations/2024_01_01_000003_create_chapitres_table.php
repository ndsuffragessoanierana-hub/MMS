<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// =============================================================================
// ECAR Masina Maria Mpanampy — Migrations Laravel
// Base de données : PostgreSQL (Supabase)
// =============================================================================
// INSTRUCTIONS D'INSTALLATION :
//   1. Copiez chaque classe dans database/migrations/ avec le nom de fichier indiqué
//   2. php artisan migrate
// =============================================================================


// =============================================================================
// FICHIER : database/migrations/2024_01_01_000003_create_chapitres_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('chapitre', function (Blueprint $table) {
            $table->string('chap_code', 20)->primary();
            $table->string('chap_libelle', 200);
            $table->char('actuel', 1)->default('O');    // O | N
        });
    }
    public function down(): void { Schema::dropIfExists('chapitre'); }
};
