<?php

namespace App\Http\Requests;

use App\Models\Cama;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DepartamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Si no se escribió un slug, se deriva del nombre: el equipo no debería tener
     * que inventar identificadores de URL a mano.
     */
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
        $departamento = $this->route('departamento');

        return [
            // Identidad
            'edificio_id' => ['required', 'exists:edificios,id'],
            'propietario_id' => ['required', 'exists:propietarios,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', Rule::unique('departamentos')->ignore($departamento)],
            'tipo' => ['required', 'string', 'max:50'],
            'piso' => ['nullable', 'integer', 'min:0', 'max:200'],
            'titular.*' => ['nullable', 'string', 'max:200'],
            'descripcion_corta.*' => ['nullable', 'string', 'max:500'],
            'descripcion_larga.*' => ['nullable', 'string', 'max:5000'],

            // Capacidad y espacio
            'capacidad_huespedes' => ['required', 'integer', 'min:1', 'max:50'],
            'dormitorios' => ['required', 'integer', 'min:0', 'max:20'],
            'banos_completos' => ['required', 'integer', 'min:0', 'max:20'],
            'banos_medios' => ['required', 'integer', 'min:0', 'max:20'],
            'superficie_m2' => ['nullable', 'numeric', 'min:0', 'max:99999'],

            // Precios y cargos
            'precio_base_noche' => ['required', 'numeric', 'min:0'],
            'moneda' => ['required', 'string', 'size:3'],
            'tarifa_limpieza' => ['required', 'numeric', 'min:0'],
            'tarifa_lavanderia' => ['required', 'numeric', 'min:0'],
            'deposito_seguridad' => ['required', 'numeric', 'min:0'],
            'huespedes_incluidos' => ['required', 'integer', 'min:1', 'lte:capacidad_huespedes'],
            'cargo_huesped_adicional' => ['required', 'numeric', 'min:0'],
            'cargo_extra_noche' => ['required', 'numeric', 'min:0'],
            'cargo_extra_concepto.*' => ['nullable', 'string', 'max:200'],

            // Descuentos
            'descuento_semanal_pct' => ['required', 'numeric', 'min:0', 'max:100'],
            'descuento_mensual_pct' => ['required', 'numeric', 'min:0', 'max:100'],
            'descuento_directo_pct' => ['required', 'numeric', 'min:0', 'max:100'],

            // Estadía
            'noches_minimas' => ['required', 'integer', 'min:1', 'max:365'],
            'noches_maximas' => ['nullable', 'integer', 'min:1', 'max:365', 'gte:noches_minimas'],
            'antelacion_minima_dias' => ['required', 'integer', 'min:0', 'max:365'],
            'ventana_reserva_meses' => ['required', 'integer', 'min:1', 'max:60'],
            'dias_preparacion' => ['required', 'integer', 'min:0', 'max:30'],

            // Reglas de la casa
            'check_in_desde' => ['required', 'date_format:H:i'],
            'check_in_hasta' => ['nullable', 'date_format:H:i', 'after:check_in_desde'],
            'check_out_hasta' => ['required', 'date_format:H:i'],
            'mascotas_permitidas' => ['boolean'],
            'deposito_mascotas' => ['required', 'numeric', 'min:0'],
            'mascotas_condiciones.*' => ['nullable', 'string', 'max:500'],
            'fumar_permitido' => ['boolean'],
            'eventos_permitidos' => ['boolean'],
            'hora_silencio' => ['nullable', 'date_format:H:i'],
            'edad_minima' => ['nullable', 'integer', 'min:0', 'max:99'],
            'reglas_adicionales.*' => ['nullable', 'string', 'max:2000'],

            // Publicación
            'publicado' => ['boolean'],
            'orden' => ['required', 'integer', 'min:0'],
            'meta_titulo.*' => ['nullable', 'string', 'max:70'],
            'meta_descripcion.*' => ['nullable', 'string', 'max:160'],

            // Amenidades y camas
            'amenidades' => ['array'],
            'amenidades.*' => ['exists:amenidades,id'],
            'destacadas' => ['array'],
            'destacadas.*' => ['exists:amenidades,id'],
            'camas' => ['array'],
            'camas.*.ambiente_es' => ['required_with:camas.*.tipo', 'nullable', 'string', 'max:100'],
            'camas.*.ambiente_en' => ['nullable', 'string', 'max:100'],
            'camas.*.tipo' => ['nullable', Rule::in(array_keys(Cama::TIPOS))],
            'camas.*.cantidad' => ['nullable', 'integer', 'min:1', 'max:20'],

            // Secciones de texto bajo la foto de portada.
            'bloques' => ['array'],
            // El id permite conservar la imagen ya cargada al reordenar o editar.
            'bloques.*.id' => ['nullable', 'integer', 'exists:bloques_contenido,id'],
            'bloques.*.imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'bloques.*.quitar_imagen' => ['nullable', 'boolean'],
            'bloques.*.antetitulo_es' => ['nullable', 'string', 'max:120'],
            'bloques.*.antetitulo_en' => ['nullable', 'string', 'max:120'],
            'bloques.*.titulo_es' => ['nullable', 'string', 'max:200'],
            'bloques.*.titulo_en' => ['nullable', 'string', 'max:200'],
            'bloques.*.cuerpo_es' => ['nullable', 'string', 'max:3000'],
            'bloques.*.cuerpo_en' => ['nullable', 'string', 'max:3000'],
            // Lista numerada opcional de la sección.
            'bloques.*.items' => ['nullable', 'array', 'max:30'],
            'bloques.*.items.*.es' => ['nullable', 'string', 'max:300'],
            'bloques.*.items.*.en' => ['nullable', 'string', 'max:300'],

            // Gestión interna
            'comision_coanfitrion_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'beds24_prop_id' => [
                'nullable', 'integer',
                Rule::unique('departamentos')
                    ->where('beds24_room_id', $this->input('beds24_room_id') ?: null)
                    ->ignore($departamento),
            ],
            'beds24_room_id' => ['nullable', 'integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'edificio_id' => 'villa',
            'propietario_id' => 'propietario',
            'capacidad_huespedes' => 'capacidad de huéspedes',
            'huespedes_incluidos' => 'huéspedes incluidos en el precio',
            'precio_base_noche' => 'precio base por noche',
            'noches_minimas' => 'noches mínimas',
            'noches_maximas' => 'noches máximas',
            'check_in_desde' => 'hora de entrada',
            'check_out_hasta' => 'hora de salida',
        ];
    }

    public function messages(): array
    {
        return [
            'huespedes_incluidos.lte' => 'Los huéspedes incluidos no pueden superar la capacidad del departamento.',
            'noches_maximas.gte' => 'Las noches máximas no pueden ser menores que las mínimas.',
            'beds24_prop_id.unique' => 'Esa combinación de Property ID y Room ID ya está asignada a otro departamento.',
        ];
    }
}
