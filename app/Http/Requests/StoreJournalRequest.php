<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'journal_mois'         => 'required|integer|between:1,12',
            'journal_annee'        => 'required|integer|min:2000|max:2100',
            'journal_solde_bni'    => 'nullable|numeric|min:0',
            'journal_solde_bfv'    => 'nullable|numeric|min:0',
            'journal_solde_caisse' => 'nullable|numeric|min:0',
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