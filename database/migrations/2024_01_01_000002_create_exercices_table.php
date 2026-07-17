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
// FICHIER : database/migrations/2024_01_01_000002_create_exercices_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercice', function (Blueprint $table) {
            $table->id('id_exercice');
            $table->smallInteger('annee')->unique();
            $table->string('libelle', 100)->nullable();
            $table->char('actif', 1)->default('O');     // O | N
        });
    }
    public function down(): void { Schema::dropIfExists('exercice'); }
};
