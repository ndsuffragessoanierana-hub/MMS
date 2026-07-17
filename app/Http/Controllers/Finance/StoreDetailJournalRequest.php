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
// FICHIER : app/Http/Requests/StoreDetailJournalRequest.php
// =============================================================================

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetailJournalRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'j_detail_date'      => 'required|date',
            'j_detail_libelle'   => 'required|string|max:500',
            'j_detail_mode_paie' => 'nullable|in:ESP,BFV,BNI,VIR,MOB',
            'j_detail_montant'   => 'required|numeric|min:0',
            'rub_rubrique_id'    => 'required|exists:t_rubrique,rubrique_id',
            'cpt_no_compte'      => 'nullable|exists:compte,no_compte',
        ];
    }

    public function messages(): array
    {
        return [
            'j_detail_date.required'    => 'La date de l\'écriture est obligatoire.',
            'j_detail_libelle.required' => 'Le libellé est obligatoire.',
            'j_detail_montant.required' => 'Le montant est obligatoire.',
            'j_detail_montant.min'      => 'Le montant ne peut pas être négatif.',
            'rub_rubrique_id.required'  => 'La rubrique est obligatoire.',
            'rub_rubrique_id.exists'    => 'Cette rubrique n\'existe pas.',
        ];
    }
}

