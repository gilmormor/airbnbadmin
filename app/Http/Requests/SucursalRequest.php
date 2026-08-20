<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('nombre')),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', Rule::unique('sucursales')->ignore($this->route('sucursal'))],
            'titular.*' => ['nullable', 'string', 'max:200'],
            'descripcion_corta.*' => ['nullable', 'string', 'max:500'],
            'descripcion_larga.*' => ['nullable', 'string', 'max:5000'],
            'como_llegar.*' => ['nullable', 'string', 'max:1000'],

            'direccion' => ['nullable', 'string', 'max:250'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'pais' => ['required', 'string', 'size:2'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],

            // Se aceptan solo formatos rasterizados a propósito: un SVG puede
            // contener scripts que se ejecutarían si alguien abre el archivo
            // directamente, y se sirve desde el mismo dominio.
            'logo' => ['nullable', 'image', 'mimes:png,webp,jpg,jpeg', 'max:2048'],
            'quitar_logo' => ['boolean'],

            // El icono de pestaña debe ser cuadrado o el navegador lo deforma.
            'favicon' => ['nullable', 'image', 'mimes:png,webp', 'max:1024', 'dimensions:ratio=1'],
            'quitar_favicon' => ['boolean'],

            'telefono' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],

            'publicada' => ['boolean'],
            'orden' => ['required', 'integer', 'min:0'],
            'meta_titulo.*' => ['nullable', 'string', 'max:70'],
            'meta_descripcion.*' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function attributes(): array
    {
        return [
            'pais' => 'país',
            'logo' => 'logo',
            'whatsapp' => 'WhatsApp',
        ];
    }

    public function messages(): array
    {
        return [
            'logo.mimes' => 'El logo debe ser PNG, WebP o JPG. Se recomienda PNG con fondo transparente.',
            'logo.max' => 'El logo debe pesar menos de 2 MB.',
            'favicon.dimensions' => 'El icono debe ser cuadrado: mismo ancho que alto. Se recomienda 512 × 512 píxeles.',
            'favicon.mimes' => 'El icono debe ser PNG o WebP.',
            'favicon.max' => 'El icono debe pesar menos de 1 MB.',
        ];
    }
}
