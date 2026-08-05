<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration de référence : table pivot déjà existante sur Supabase.
     * Clé composite (emp_idplace, fit_idfitaovana) -> gérée manuellement
     * via DB::table()->updateOrInsert() comme le reste du projet.
     */
    public function up(): void
    {
        if (!Schema::hasTable('empla_fitaovana')) {
            Schema::create('empla_fitaovana', function (Blueprint $table) {
                $table->string('emp_idplace');
                $table->integer('fit_idfitaovana');

                $table->primary(['emp_idplace', 'fit_idfitaovana']);

                $table->foreign('emp_idplace')
                    ->references('idplace')->on('emplacement')
                    ->onDelete('cascade');

                $table->foreign('fit_idfitaovana')
                    ->references('idfitaovana')->on('fitaovana')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('empla_fitaovana');
    }
};
