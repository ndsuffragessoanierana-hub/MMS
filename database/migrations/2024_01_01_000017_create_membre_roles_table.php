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
// FICHIER : database/migrations/2024_01_01_000017_create_membre_roles_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('membre_role', function (Blueprint $table) {
            $table->string('code',4)->primary();
            $table->string('libelle', 100);
        });
    }
    public function down(): void { Schema::dropIfExists('membre_role'); }
};
