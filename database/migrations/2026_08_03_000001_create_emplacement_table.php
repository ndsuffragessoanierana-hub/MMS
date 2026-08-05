<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration de référence : la table "emplacement" existe déjà sur Supabase.
     * Le hasTable() évite toute erreur si elle est exécutée par erreur,
     * elle sert surtout à documenter le schéma dans le versioning Laravel.
     */
    public function up(): void
    {
        if (!Schema::hasTable('emplacement')) {
            Schema::create('emplacement', function (Blueprint $table) {
                $table->string('idplace')->primary();
                $table->string('libelle_place');
                $table->string('toerana')->nullable();
                $table->string('nom_court')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('emplacement');
    }
};
