<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mois', function (Blueprint $table) {
            $table->smallInteger('numero')->primary();
            $table->string('libelle_mois_fr', 20);
            $table->string('libelle_mois_en', 20);
        });

        DB::table('mois')->insert([
            ['numero' => 1,  'libelle_mois_fr' => 'Janvier',   'libelle_mois_en' => 'January'],
            ['numero' => 2,  'libelle_mois_fr' => 'Février',   'libelle_mois_en' => 'February'],
            ['numero' => 3,  'libelle_mois_fr' => 'Mars',      'libelle_mois_en' => 'March'],
            ['numero' => 4,  'libelle_mois_fr' => 'Avril',     'libelle_mois_en' => 'April'],
            ['numero' => 5,  'libelle_mois_fr' => 'Mai',       'libelle_mois_en' => 'May'],
            ['numero' => 6,  'libelle_mois_fr' => 'Juin',      'libelle_mois_en' => 'June'],
            ['numero' => 7,  'libelle_mois_fr' => 'Juillet',   'libelle_mois_en' => 'July'],
            ['numero' => 8,  'libelle_mois_fr' => 'Août',      'libelle_mois_en' => 'August'],
            ['numero' => 9,  'libelle_mois_fr' => 'Septembre', 'libelle_mois_en' => 'September'],
            ['numero' => 10, 'libelle_mois_fr' => 'Octobre',   'libelle_mois_en' => 'October'],
            ['numero' => 11, 'libelle_mois_fr' => 'Novembre',  'libelle_mois_en' => 'November'],
            ['numero' => 12, 'libelle_mois_fr' => 'Décembre',  'libelle_mois_en' => 'December'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('mois');
    }
};
