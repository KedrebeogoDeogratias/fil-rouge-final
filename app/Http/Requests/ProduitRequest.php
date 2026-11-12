<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'designationP' => [$required, 'string', 'max:255'],
            'categorie'    => [$required, 'string'],
            'prixHt'       => [$required, 'numeric', 'min:0'],
            'promotionP'   => ['nullable', 'integer', 'min:0', 'max:100'],
            'stockP'       => [$required, 'integer', 'min:0'],
            'imageP'       => ['nullable', 'string'],
        ];
    }
}
