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
// FICHIER : database/migrations/2024_01_01_000007_create_t_detail_journals_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_detail_journal', function (Blueprint $table) {
            $table->id('j_detail_numero');
            $table->date('j_detail_date');
            $table->string('j_detail_libelle', 500);
            $table->string('j_detail_mode_paie', 50)->nullable(); // ESPECE|CHEQUE|VIREMENT
            $table->decimal('j_detail_montant', 15, 2)->default(0);

            // FK → t_journals
            $table->unsignedBigInteger('jrl_journal_id');
            $table->foreign('jrl_journal_id')
                  ->references('journal_id')
                  ->on('t_journal')
                  ->cascadeOnDelete();

            // FK → t_rubriques (clé primaire string)
            $table->string('rub_rubrique_id', 20);
            $table->foreign('rub_rubrique_id')
                  ->references('rubrique_id')
                  ->on('t_rubrique')
                  ->restrictOnDelete();

            // FK → comptes (clé primaire string, nullable)
            $table->string('cpt_no_compte', 50)->nullable();
            $table->foreign('cpt_no_compte')
                  ->references('no_compte')
                  ->on('compte')
                  ->nullOnDelete();


            // Index fréquents
            $table->index('j_detail_date');
            $table->index('jrl_journal_id');
            $table->index('rub_rubrique_id');
        });
    }
    public function down(): void { Schema::dropIfExists('t_detail_journal'); }
};
