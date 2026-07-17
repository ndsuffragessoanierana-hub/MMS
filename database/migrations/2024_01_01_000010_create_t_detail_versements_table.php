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
// FICHIER : database/migrations/2024_01_01_000010_create_t_detail_versements_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_detail_versement', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('j_detail_numero');
            $table->foreign('j_detail_numero')
                  ->references('j_detail_numero')
                  ->on('t_detail_journal')
                  ->cascadeOnDelete();
            $table->decimal('montant', 15, 2)->default(0);
            $table->date('date_versement')->nullable();
            $table->text('libelle')->nullable();
        });
    }
    public function down(): void { Schema::dropIfExists('t_detail_versement'); }
};
