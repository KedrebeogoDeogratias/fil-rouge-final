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
        return [
            'designation_p' => ['required', 'string', 'max:255'],
            'type_p'        => ['required', 'string', 'max:255'],
            'prix_ht'       => ['required', 'numeric', 'min:0'],
            'promotion_p'   => ['nullable', 'integer', 'min:0', 'max:100'],
            'stock_p'       => ['required', 'integer', 'min:0'],
            'image_p'       => ['nullable', 'string'],
        ];
    }
}