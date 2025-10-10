<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'         => 'required|max:50',
            'price'        => 'required|numeric',
            'available'    => 'required|in:true,false,1,0',
            'unit_measure' => 'required|string|max:20',
            'stock'        => 'required|integer|min:0',
            'category'     => 'required|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'O nome do produto é obrigatório',
            'price.required'        => 'O preço do produto é obrigatório',
            'image.required'        => 'O campo de imagem é obrigatório',
            'available.required'    => 'O campo de disponibilidade é obrigatório',
            'unit_measure.required' => 'A unidade de medida é obrigatória',
            'stock.required'        => 'O estoque é obrigatório',
            'name.max'              => 'O nome do produto não pode exceder 50 caracteres',
            'unit_measure.max'      => 'A unidade de medida não pode exceder 20 caracteres',
            'image.image'           => 'O arquivo enviado deve ser uma imagem válida',
            'available.in'          => 'O campo de disponibilidade deve ser verdadeiro ou falso',
            'stock.integer'         => 'O estoque deve ser um número inteiro',
            'stock.min'             => 'O estoque não pode ser negativo'
        ];
    }
}
