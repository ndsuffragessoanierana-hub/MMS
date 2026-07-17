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
// FICHIER : database/migrations/2024_01_01_000016_create_fikambananas_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('fikambanana', function (Blueprint $table) {
            
            $table->string('idfikambanana',5)->primary();
            $table->string('libelle_fikambanana', 200);
            $table->string('st_patron', 100)->nullable();

            $table->index('idfikambanana');

        });
    }
    public function down(): void { Schema::dropIfExists('fikambanana'); }
};
