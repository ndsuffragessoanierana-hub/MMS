<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_detail_versement', function (Blueprint $table) {
            $table->unsignedBigInteger('j_detail_numero');
            $table->unsignedInteger('id');
            $table->string('libelle', 100);
            $table->decimal('montant', 15, 2);
            $table->string('remarque', 100)->nullable();

            $table->primary(['j_detail_numero', 'id']);
            $table->foreign('j_detail_numero')
                  ->references('j_detail_numero')
                  ->on('t_detail_journal')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_detail_versement');
    }
};