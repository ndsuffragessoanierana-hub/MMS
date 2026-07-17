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
// FICHIER : app/Http/Requests/StoreRubriqueRequest.php
// =============================================================================

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRubriqueRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        $rubId = $this->route('rubrique')?->rubrique_id ?? 'new';
        return [
            'rubrique_id'      => "sometimes|required|string|max:20|unique:t_rubrique,rubrique_id,{$rubId},rubrique_id",
            'rubrique_libelle' => 'required|string|max:200',
            'chap_code'        => 'required|exists:chapitre,chap_code',
        ];
    }
}
