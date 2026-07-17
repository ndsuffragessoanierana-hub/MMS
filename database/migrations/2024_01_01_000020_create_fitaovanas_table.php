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
// FICHIER : database/migrations/2024_01_01_000020_create_fitaovanas_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('fitaovana', function (Blueprint $table) {
            $table->id('idfitaovana');
            $table->string('denomination', 300);
            $table->string('reference', 100)->nullable();
            $table->date('date_acquisition')->nullable();
            $table->decimal('valeur_acquisition', 15, 2)->nullable();
            $table->integer('qte_achetee')->default(1);
            $table->string('fournisseur', 200)->nullable();
            $table->string('no_inventaire', 50)->nullable()->unique();
            $table->string('tf_id_type_fitaovana')->nullable();
            $table->foreign('tf_id_type_fitaovana')
                  ->references('id_type_fitaovana')
                  ->on('type_fitaovana')
                  ->nullOnDelete();
            $table->text('remarque')->nullable();
            $table->text('qr_text')->nullable();    // Données du QR code


            $table->index('tf_id_type_fitaovana');
        });
    }
    public function down(): void { Schema::dropIfExists('fitaovana'); }
};
