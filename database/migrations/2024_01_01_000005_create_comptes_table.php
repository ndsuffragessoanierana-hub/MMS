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
// FICHIER : database/migrations/2024_01_01_000005_create_comptes_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('compte', function (Blueprint $table) {
            $table->string('no_compte', 50)->primary();
            $table->string('libelle_compte', 200);
            $table->string('type_compte', 30)->nullable();  // CAISSE, BNI, BFV…
            $table->char('actif', 1)->default('O');
        });
    }
    public function down(): void { Schema::dropIfExists('compte'); }
};
