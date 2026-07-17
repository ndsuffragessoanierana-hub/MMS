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
// FICHIER : database/migrations/2024_01_01_000014_create_apvs_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('apv', function (Blueprint $table) {
            $table->string('idapv',5)->primary();
            $table->string('libelle_apv', 100);
            $table->string('idfaritra');
            $table->foreign('idfaritra')
                  ->references('idfaritra')
                  ->on('faritra')
                  ->cascadeOnDelete();


            $table->index('idfaritra');
        });
    }
    public function down(): void { Schema::dropIfExists('apv'); }
};

