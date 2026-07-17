<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FideleController;
use App\Http\Controllers\FaritraController;
use App\Http\Controllers\ApvController;
use App\Http\Controllers\FikambananaController;
use App\Http\Controllers\InventaireController;
use App\Http\Controllers\TypeFitaovanaController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Finance\JournalController;
use App\Http\Controllers\Finance\RubriqueController;
use App\Http\Controllers\Finance\ChapitreController;
use App\Http\Controllers\Finance\RecapController;
use App\Http\Controllers\Finance\BudgetController;
use App\Http\Controllers\Finance\ExerciceController;
use Illuminate\Support\Facades\Route;

// ── Page d'accueil publique ──
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ── Profil Breeze ──
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Application ECAR ──
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Module Fidèles ──
    Route::resource('fideles',      FideleController::class);
    Route::resource('faritraS',     FaritraController::class);
    Route::resource('apvs',         ApvController::class);
    Route::resource('fikambananas', FikambananaController::class);
    Route::get('apv-par-faritra/{id}', [FideleController::class, 'apvParFaritra'])
         ->name('apv.par.faritra');
    Route::post('fikambananas/{fikambanana}/membres',
         [FikambananaController::class, 'ajouterMembre'])->name('fikambananas.membres.ajouter');
    Route::delete('fikambananas/{fikambanana}/membres/{matricule}',
         [FikambananaController::class, 'retirerMembre'])->name('fikambananas.membres.retirer');

    // ── Module Inventaire ──
    Route::get('inventaire/pdf', [InventaireController::class, 'exportPdf'])->name('inventaire.pdf');
    Route::resource('inventaire',      InventaireController::class);
    Route::resource('type-fitaovanas', TypeFitaovanaController::class);

    // ── Agenda ──
    Route::resource('agenda', AgendaController::class);

    // ── Module Finances ──
    Route::prefix('finances')->name('finances.')->group(function () {

        // Livre Journal
        Route::resource('journals', JournalController::class);
        Route::post('journals/{journal}/ecritures',
            [JournalController::class, 'storeEcriture'])->name('journals.ecritures.store');
        Route::put('ecritures/{detail}',
            [JournalController::class, 'updateEcriture'])->name('ecritures.update');
        Route::delete('ecritures/{detail}',
            [JournalController::class, 'destroyEcriture'])->name('ecritures.destroy');

        // Rubriques & Chapitres
        Route::resource('rubriques', RubriqueController::class)->except(['show','create','edit']);
        Route::resource('chapitres', ChapitreController::class)->except(['show','create','edit']);

        // Récapitulatifs
        Route::get('recap/rubrique',  [RecapController::class, 'parRubrique'])->name('recap.rubrique');
        Route::get('recap/chapitre',  [RecapController::class, 'parChapitre'])->name('recap.chapitre');
        Route::get('recap/evolution', [RecapController::class, 'evolutionSolde'])->name('recap.evolution');
        Route::get('recap/compte',    [RecapController::class, 'parCompte'])->name('recap.compte');

        // PDFs Finances ← dans le groupe finances pour avoir le préfixe finances.
        Route::get('journals/{journal}/pdf', [JournalController::class, 'exportPdf'])
             ->name('journals.pdf');
        Route::get('recap/rubrique/pdf',     [RecapController::class,   'pdfRubrique'])
             ->name('recap.rubrique.pdf');
        Route::get('recap/chapitre/pdf',     [RecapController::class,   'pdfChapitre'])
             ->name('recap.chapitre.pdf');
        Route::get('recap/compte/pdf',       [RecapController::class,   'pdfCompte'])
             ->name('recap.compte.pdf');

        // Budget
        Route::get('budget/annuel',   [BudgetController::class, 'annuel'])->name('budget.annuel');
        Route::post('budget/annuel',  [BudgetController::class, 'storeAnnuel'])->name('budget.annuel.store');
        Route::get('budget/mensuel',  [BudgetController::class, 'mensuel'])->name('budget.mensuel');
        Route::post('budget/mensuel', [BudgetController::class, 'storeMensuel'])->name('budget.mensuel.store');

        // Exercices
        Route::post('exercices', [ExerciceController::class, 'store'])->name('exercices.store');
    });
});

require __DIR__.'/auth.php';
