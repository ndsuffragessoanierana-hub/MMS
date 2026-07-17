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
// FICHIER : app/Http/Requests/StoreFideleRequest.php
// =============================================================================

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFideleRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            // Identité
            'matricule'         => 'required|string|max:20|unique:fideles,matricule',
            'nom'               => 'required|string|max:100',
            'prenom'            => 'required|string|max:100',
            'nom_bapteme'       => 'nullable|string|max:100',
            'sexe'              => 'nullable|in:M,F',

            // Naissance
            'date_naissance'    => 'nullable|date|before:today',
            'lieu_naissance'    => 'nullable|string|max:200',

            // Sacrements
            'date_bapteme'      => 'nullable|date',
            'lieu_bapteme'      => 'nullable|string|max:200',
            'nom_pretre'        => 'nullable|string|max:100',
            'tuteur'            => 'nullable|string|max:200',
            'date_confesse'     => 'nullable|date',
            'date_communion'    => 'nullable|date',
            'date_confirmation' => 'nullable|date',
            'date_mariage'      => 'nullable|date',
            'date_ordination'   => 'nullable|date',
            'date_deces'        => 'nullable|date',

            // Famille
            'nom_pere'          => 'nullable|string|max:200',
            'nom_mere'          => 'nullable|string|max:200',
            'numero_famille'    => 'nullable|string|max:50',

            // Paroisse
            'idfaritra'         => 'nullable|exists:faritraS,idfaritra',
            'idapv'             => 'nullable|exists:apvs,idapv',
            'date_arrivee'      => 'nullable|date',
            'date_integration'  => 'nullable|date',
            'adresse'           => 'nullable|string',
            'quitte'            => 'nullable|in:O,N',
            'statut'            => 'nullable|string|max:50',
            'numero_registre'   => 'nullable|string|max:50',
            'profession'        => 'nullable|string|max:200',
            'observation'       => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'matricule.required'  => 'Le matricule est obligatoire.',
            'matricule.unique'    => 'Ce matricule existe déjà.',
            'nom.required'        => 'Le nom de famille est obligatoire.',
            'prenom.required'     => 'Le prénom est obligatoire.',
            'sexe.in'             => 'Le sexe doit être M ou F.',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'idfaritra.exists'    => 'Le Faritra sélectionné n\'existe pas.',
            'idapv.exists'        => 'L\'APV sélectionné n\'existe pas.',
        ];
    }
}