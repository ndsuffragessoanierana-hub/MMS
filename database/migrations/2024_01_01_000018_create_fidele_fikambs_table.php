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
// FICHIER : database/migrations/2024_01_01_000018_create_fidele_fikambs_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('fidele_fikamb', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('idfikambanana');
            $table->foreign('idfikambanana')
                  ->references('idfikambanana')
                  ->on('fikambanana')
                  ->cascadeOnDelete();

            $table->string('matricule', 20);
            $table->foreign('matricule')
                  ->references('matricule')
                  ->on('fidele')
                  ->cascadeOnDelete();

            $table->date('date_adhesion')->nullable();

            $table->string('code', 4)->nullable();
            $table->foreign('code')
                ->references('code')
                ->on('membre_role')
                ->nullOnDelete();

            $table->unique(['idfikambanana', 'matricule']);
            $table->index('matricule');
            $table->index('idfikambanana');
        });
    }
    public function down(): void { Schema::dropIfExists('fidele_fikamb'); }
};