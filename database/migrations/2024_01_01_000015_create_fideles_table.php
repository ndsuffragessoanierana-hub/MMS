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
// FICHIER : database/migrations/2024_01_01_000015_create_fideles_table.php
// =============================================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('fidele', function (Blueprint $table) {
            $table->string('matricule', 20)->primary();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('nom_bapteme', 100)->nullable();
            $table->char('sexe', 1)->nullable();            // M | F

            // Naissance
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance', 200)->nullable();

            // Sacrements
            $table->date('date_bapteme')->nullable();
            $table->string('lieu_bapteme', 200)->nullable();
            $table->string('nom_pretre', 100)->nullable();
            $table->string('tuteur', 200)->nullable();
            $table->date('date_confesse')->nullable();
            $table->date('date_communion')->nullable();
            $table->date('date_confirmation')->nullable();
            $table->date('date_mariage')->nullable();
            $table->date('date_ordination')->nullable();
            $table->date('date_deces')->nullable();

            // Famille
            $table->string('nom_pere', 200)->nullable();
            $table->string('nom_mere', 200)->nullable();
            $table->string('numero_famille', 50)->nullable();

            // Paroisse
            $table->string('idfaritra')->nullable();
            $table->foreign('idfaritra')
                  ->references('idfaritra')
                  ->on('faritra')
                  ->nullOnDelete();

            $table->string('idapv')->nullable();
            $table->foreign('idapv')
                  ->references('idapv')
                  ->on('apv')
                  ->nullOnDelete();

            $table->date('date_arrivee')->nullable();
            $table->date('date_integration')->nullable();
            $table->text('adresse')->nullable();
            $table->char('quitte', 1)->default('N');       // O | N
            $table->string('statut', 50)->nullable();      // actif | parti | décédé
            $table->string('numero_registre', 50)->nullable();
            $table->string('profession', 200)->nullable();
            $table->text('observation')->nullable();


            // Index fréquents
            $table->index(['nom', 'prenom']);
            $table->index('idfaritra');
            $table->index('idapv');
            $table->index('quitte');
            $table->index('statut');
        });
    }
    public function down(): void { Schema::dropIfExists('fidele'); }
};