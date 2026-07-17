<?php
// =============================================================================
// ECAR — Module Finances : CRUD complet
// Livre Journal · Rubriques · Chapitres · Comptes · Budget · Récapitulatif
// =============================================================================
// Fichiers à créer :
//   app/Http/Controllers/Finance/JournalController.php
//   app/Http/Controllers/Finance/RubriqueController.php
//   app/Http/Controllers/Finance/ChapitreController.php
//   app/Http/Controllers/Finance/CompteController.php
//   app/Http/Controllers/Finance/BudgetController.php
//   app/Http/Controllers/Finance/RecapController.php
//   app/Http/Requests/StoreJournalRequest.php
//   app/Http/Requests/StoreDetailJournalRequest.php
//   app/Http/Requests/StoreRubriqueRequest.php
//   routes/web.php (extrait finances)
//   resources/views/finances/journal/index.blade.php
//   resources/views/finances/journal/show.blade.php
//   resources/views/finances/journal/_form_ecriture.blade.php
//   resources/views/finances/rubriques/index.blade.php
//   resources/views/finances/recap/index.blade.php
//   resources/views/finances/dashboard.blade.php
// =============================================================================


// =============================================================================
// FICHIER : app/Http/Requests/StoreJournalRequest.php
// =============================================================================

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJournalRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'journal_mois'          => 'required|integer|between:1,12',
            'journal_annee'         => 'required|integer|min:2000|max:2100',
            'journal_solde_bni'     => 'nullable|numeric|min:0',
            'journal_solde_bfv'     => 'nullable|numeric|min:0',
            'journal_solde_caisse'  => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'journal_mois.between' => 'Le mois doit être entre 1 et 12.',
            'journal_annee.min'    => 'L\'année doit être au moins 2000.',
        ];
    }
}

