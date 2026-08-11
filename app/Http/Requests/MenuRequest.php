<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_id' => [
                'nullable', 'exists:menus,id',
                Rule::notIn([$this->route('menu')?->id]),
            ],
            'nombre' => ['required', 'string', 'max:100'],
            'ruta' => ['nullable', 'string', 'max:150'],
            'icono' => ['nullable', 'string', 'max:60'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
