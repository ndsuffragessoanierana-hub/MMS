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
// FICHIER : database/migrations/2024_01_01_000006_create_t_journals_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_journal', function (Blueprint $table) {
            $table->id('journal_id');
            $table->smallInteger('journal_mois');
            $table->smallInteger('journal_annee');
            $table->decimal('journal_solde_bni',   15, 2)->default(0);
            $table->decimal('journal_solde_bfv',   15, 2)->default(0);
            $table->decimal('journal_solde_caisse', 15, 2)->default(0);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->unique(['journal_mois', 'journal_annee']);

            $table->foreign('journal_mois')
                  ->references('numero')
                  ->on('mois');
        });
    }
    public function down(): void { Schema::dropIfExists('t_journal'); }
};

