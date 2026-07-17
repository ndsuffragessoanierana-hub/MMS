<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRubriqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rubriqueId = $this->route('rubrique');

        return [
            'rubrique_id'      => 'sometimes|required|string|max:20|unique:t_rubrique,rubrique_id,' . $rubriqueId . ',rubrique_id',
            'rubrique_libelle' => 'required|string|max:200',
            'chap_code'        => 'required|exists:chapitre,chap_code',
        ];
    }

    public function messages(): array
    {
        return [
            'rubrique_id.required'   => 'Le code rubrique est obligatoire.',
            'rubrique_id.unique'     => 'Ce code rubrique existe déjà.',
            'rubrique_id.max'        => 'Le code ne doit pas dépasser 20 caractères.',
            'rubrique_libelle.required' => 'Le libellé est obligatoire.',
            'chap_code.required'     => 'Le chapitre est obligatoire.',
            'chap_code.exists'       => 'Ce chapitre n\'existe pas.',
        ];
    }
}