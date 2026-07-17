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
// FICHIER : database/migrations/2024_01_01_000004_create_t_rubriques_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_rubrique', function (Blueprint $table) {
            $table->string('rubrique_id', 20)->primary();
            $table->string('rubrique_libelle', 200);
            $table->string('chap_code', 20);
            $table->date('date_saisie')->nullable()->useCurrent();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreign('chap_code')
                  ->references('chap_code')
                  ->on('chapitre')
                  ->restrictOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('t_rubrique'); }
};

