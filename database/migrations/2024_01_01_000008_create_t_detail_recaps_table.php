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
// FICHIER : database/migrations/2024_01_01_000008_create_t_detail_recaps_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_detail_recap', function (Blueprint $table) {
            $table->id();
            $table->string('rub_rubrique_id', 20);
            $table->smallInteger('rec_rec_mois');
            $table->smallInteger('rec_rec_annee');
            $table->decimal('detail_rkp_montant', 15, 2)->default(0);

            $table->foreign('rub_rubrique_id')
                  ->references('rubrique_id')
                  ->on('t_rubrique');

            $table->foreign('rec_rec_mois')
                  ->references('numero')
                  ->on('mois');

            $table->index(['rec_rec_annee', 'rec_rec_mois']);
        });
    }
    public function down(): void { Schema::dropIfExists('t_detail_recap'); }
};
