<?php
// =============================================================================
// ECAR — Module Fidèles : CRUD complet
// Liber Status Animarum (Registre des fidèles)
// =============================================================================
// Fichiers à créer :
//   app/Http/Controllers/FideleController.php
//   app/Http/Requests/StoreFideleRequest.php
//   app/Http/Requests/UpdateFideleRequest.php
//   routes/web.php  (extrait)
//   resources/views/fideles/index.blade.php
//   resources/views/fideles/create.blade.php
//   resources/views/fideles/edit.blade.php
//   resources/views/fideles/show.blade.php
//   resources/views/fideles/_form.blade.php
// =============================================================================


// =============================================================================
// FICHIER : app/Http/Requests/UpdateFideleRequest.php
// =============================================================================

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFideleRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        // Mêmes règles sauf matricule (on ne change pas la clé primaire)
        $rules = (new StoreFideleRequest)->rules();
        $rules['matricule'] = 'prohibited'; // ne peut pas être modifié
        return $rules;
    }
}
