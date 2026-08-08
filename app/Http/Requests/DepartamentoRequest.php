<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'edificio_id' => ['required', 'exists:edificios,id'],
            'propietario_id' => ['required', 'exists:propietarios,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'comision_coanfitrion_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'beds24_prop_id' => [
                'nullable', 'integer',
                Rule::unique('departamentos')
                    ->where('beds24_room_id', $this->input('beds24_room_id') ?: null)
                    ->ignore($this->route('departamento')),
            ],
            'beds24_room_id' => ['nullable', 'integer'],
        ];
    }
}
