<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

/**
 * Servicios que el huésped puede agregar a su reserva.
 *
 * ATENCIÓN: el precio del servicio de chef está inconsistente en el sitio actual.
 * La portada anuncia desde US$45 (grupos de 6 a 8) y US$165 (hasta 14 personas,
 * US$55 por comida), mientras que el FAQ y las fichas de cada unidad dicen US$35.
 * Aquí se cargó US$35 por ser el valor que más se repite, pero hay que confirmarlo
 * con el cliente antes de publicar.
 */
class ServiciosSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            [
                'slug' => 'chef-comida',
                'nombre' => ['es' => 'Servicio de chef', 'en' => 'Chef service'],
                'descripcion' => [
                    'es' => 'Preparación de almuerzo o cena para el grupo. Los huéspedes ponen los víveres.',
                    'en' => 'Lunch or dinner prepared for the group. Guests supply the groceries.',
                ],
                'precio' => 35.00,
                'tipo_cobro' => 'por_comida',
                'icono' => 'bi-egg-fried',
            ],
            [
                'slug' => 'chef-desayuno',
                'nombre' => ['es' => 'Desayuno de chef', 'en' => 'Chef breakfast'],
                'descripcion' => [
                    'es' => 'Desayuno preparado por nuestro chef. Cortesía en las unidades de Riberamar.',
                    'en' => 'Breakfast prepared by our chef. Complimentary at Riberamar units.',
                ],
                'precio' => 0.00,
                'tipo_cobro' => 'por_comida',
                'icono' => 'bi-cup-hot',
            ],
            [
                'slug' => 'carrito-golf',
                'nombre' => ['es' => 'Alquiler de carrito de golf', 'en' => 'Golf cart rental'],
                'descripcion' => [
                    'es' => 'Carrito de golf para moverse por el complejo y la zona.',
                    'en' => 'Golf cart to get around the complex and the area.',
                ],
                'precio' => 0.00,
                'tipo_cobro' => 'por_noche',
                'icono' => 'bi-car-front',
            ],
            [
                'slug' => 'cuatrimoto',
                'nombre' => ['es' => 'Alquiler de cuatrimoto', 'en' => 'ATV rental'],
                'descripcion' => [
                    'es' => 'Cuatrimoto para explorar Las Terrenas y sus alrededores.',
                    'en' => 'ATV to explore Las Terrenas and its surroundings.',
                ],
                'precio' => 0.00,
                'tipo_cobro' => 'por_noche',
                'icono' => 'bi-bicycle',
            ],
            [
                'slug' => 'aire-central-penthouse',
                'nombre' => ['es' => 'Aire central en sala y comedor', 'en' => 'Central air in living and dining'],
                'descripcion' => [
                    'es' => 'Cargo adicional por el uso del aire central en las áreas comunes del penthouse.',
                    'en' => 'Additional charge for central air use in the penthouse common areas.',
                ],
                'precio' => 20.00,
                'tipo_cobro' => 'por_noche',
                'icono' => 'bi-snow',
            ],
            [
                'slug' => 'descuento-restaurante',
                'nombre' => ['es' => 'Descuento en restaurante', 'en' => 'Restaurant discount'],
                'descripcion' => [
                    'es' => 'Genera un código QR con 10% de descuento para presentar en el restaurante.',
                    'en' => 'Generates a QR code with 10% off to present at the restaurant.',
                ],
                'precio' => 0.00,
                'tipo_cobro' => 'por_reserva',
                'icono' => 'bi-qr-code',
            ],
        ];

        foreach ($servicios as $orden => $servicio) {
            Servicio::updateOrCreate(
                ['slug' => $servicio['slug']],
                $servicio + ['moneda' => 'USD', 'activo' => true, 'orden' => $orden]
            );
        }
    }
}
