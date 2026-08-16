<?php

namespace Database\Seeders;

use App\Models\Amenidad;
use Illuminate\Database\Seeder;

/**
 * Catálogo base de amenidades, tomado de lo que ya anuncian las fichas del sitio
 * actual y el FAQ. Se mantiene como catálogo y no como texto libre para poder
 * filtrar el buscador por criterios como "con piscina privada".
 */
class AmenidadesSeeder extends Seeder
{
    public function run(): void
    {
        $amenidades = [
            // Climatización
            ['aire-dormitorios', 'Aire acondicionado en dormitorios', 'Air conditioning in bedrooms', 'climatizacion'],
            ['aire-central', 'Aire acondicionado central', 'Central air conditioning', 'climatizacion'],
            ['ventilador-techo', 'Ventilador de techo', 'Ceiling fan', 'climatizacion'],

            // Cocina
            ['cocina-equipada', 'Cocina totalmente equipada', 'Fully equipped kitchen', 'cocina'],
            ['comedor', 'Comedor', 'Dining room', 'cocina'],
            ['bbq', 'Parrilla BBQ', 'BBQ grill', 'cocina'],
            ['cafetera', 'Cafetera', 'Coffee maker', 'cocina'],

            // Exterior
            ['piscina-privada', 'Piscina privada', 'Private pool', 'exterior'],
            ['piscina-comun', 'Piscina común del complejo', 'Shared complex pool', 'exterior'],
            ['jacuzzi', 'Jacuzzi climatizado', 'Heated jacuzzi', 'exterior'],
            ['terraza', 'Terraza', 'Terrace', 'exterior'],
            ['terraza-techo', 'Terraza en la azotea', 'Rooftop terrace', 'exterior'],
            ['jardin-privado', 'Jardín privado', 'Private garden', 'exterior'],
            ['area-picnic', 'Área de picnic', 'Picnic area', 'exterior'],
            ['vista-mar', 'Vista al mar', 'Ocean view', 'exterior'],
            ['chaise-lounge', 'Sillas de playa', 'Chaise lounge', 'exterior'],

            // Entretenimiento
            ['wifi', 'Wi-Fi', 'Wi-Fi', 'entretenimiento'],
            ['tv-plasma', 'TV de pantalla plana', 'Flat screen TV', 'entretenimiento'],
            ['cable-satelital', 'Cable y satélite', 'Cable and satellite', 'entretenimiento'],
            ['home-theater', 'Home theater', 'Home theater', 'entretenimiento'],

            // Seguridad
            ['seguridad-24h', 'Seguridad 24 horas', '24 hour security', 'seguridad'],
            ['caja-fuerte', 'Caja fuerte', 'Safety deposit box', 'seguridad'],
            ['generador', 'Planta eléctrica de respaldo', 'Backup power generator', 'seguridad'],

            // Servicios
            ['limpieza-diaria', 'Limpieza diaria incluida', 'Daily cleaning included', 'servicios'],
            ['conserje', 'Servicio de conserjería', 'Concierge service', 'servicios'],
            ['lavadora', 'Lavadora', 'Washing machine', 'servicios'],
            ['tendedero', 'Tendedero', 'Clothesline', 'servicios'],
            ['electricidad-incluida', 'Electricidad incluida', 'Electricity included', 'servicios'],
            ['estacionamiento', 'Estacionamiento', 'Parking', 'servicios'],
            ['toallas-playa', 'Toallas de playa y piscina', 'Beach and pool towels', 'servicios'],
        ];

        foreach ($amenidades as $orden => [$slug, $es, $en, $categoria]) {
            Amenidad::updateOrCreate(
                ['slug' => $slug],
                [
                    'nombre' => ['es' => $es, 'en' => $en],
                    'categoria' => $categoria,
                    'orden' => $orden,
                    'activa' => true,
                ]
            );
        }
    }
}
