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
// FICHIER : database/migrations/2024_01_01_000011_create_ligne_budgets_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('ligne_budget', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_exercice');
            $table->foreign('id_exercice')
                  ->references('id_exercice')
                  ->on('exercice')
                  ->cascadeOnDelete();
            $table->integer('lg_bdg_numero');
            $table->string('rub_rubrique_id', 20);
            $table->foreign('rub_rubrique_id')
                  ->references('rubrique_id')
                  ->on('t_rubrique');
            $table->decimal('lg_bdg_montant', 15, 2)->default(0);

            $table->unique(['id_exercice', 'rub_rubrique_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('ligne_budget'); }
};


