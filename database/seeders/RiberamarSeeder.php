<?php

namespace Database\Seeders;

use App\Models\Amenidad;
use App\Models\Cama;
use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\Propietario;
use App\Models\Resena;
use App\Models\Servicio;
use Illuminate\Database\Seeder;

/**
 * Carga Villa Riberamar y sus cinco unidades a partir del contenido extraído del
 * sitio actual de WordPress (database/contenido-inicial/).
 *
 * PENDIENTE DE CONFIRMAR CON EL CLIENTE:
 *  - Precio por noche de cada unidad: no figura en el sitio actual, queda en 0.
 *  - Número de baños: solo se dedujo, las fichas no lo detallan.
 *  - Política de cancelación.
 *  - Precio real del servicio de chef (US$35 vs US$45 vs US$165).
 *  - IDs de Beds24 de cada unidad.
 */
class RiberamarSeeder extends Seeder
{
    public function run(): void
    {
        $propietario = Propietario::firstOrCreate(
            ['nombre' => 'Villa Riberamar'],
            ['email' => 'reservations@riberamar.com', 'telefono' => '+1 849 382 2222']
        );

        $villa = Edificio::updateOrCreate(
            ['slug' => 'villa-riberamar'],
            [
                'nombre' => 'Villa Riberamar',
                'titular' => [
                    'es' => 'Un descanso exclusivo, placentero y seguro',
                    'en' => 'An exclusive, pleasing and safe break',
                ],
                'descripcion_corta' => [
                    'es' => 'Buenos momentos, comida exquisita, servicio personalizado, privacidad y relajación frente a Playa Las Ballenas.',
                    'en' => 'Good times, exquisite food, personalized service, privacy, pleasure and relaxation by Playa Las Ballenas.',
                ],
                'descripcion_larga' => [
                    'es' => 'Una propiedad exclusiva diseñada para preservar la esencia de su entorno, conservando las áreas verdes y el Río Salado que desemboca en el mar, escondida en una cala apartada junto a Playa Las Ballenas. Imagina todos los servicios de un hotel cinco estrellas en tu apartamento privado: conserjería para reservar cenas y excursiones, seguridad 24 horas y limpieza diaria sin costo adicional.',
                    'en' => 'An exclusive property designed to preserve the essence of its surroundings, conserving the green areas and the Salt River running into the sea, tucked away in a secluded cove by Las Ballenas Beach. Imagine all the services of a five star hotel in your private apartment: concierge for dinner reservations and trips, 24/7 security, and daily cleaning at no additional cost.',
                ],
                'direccion' => 'Complejo Bonita Village, Playa Las Ballenas',
                'ciudad' => 'Las Terrenas',
                'provincia' => 'Samaná',
                'pais' => 'DO',
                'como_llegar' => [
                    'es' => 'Playa Las Ballenas queda a unos 7 minutos a pie, cruzando el jardín del complejo donde está la piscina común. Tiendas, supermercados y bancos a 10-12 minutos.',
                    'en' => 'Playa Las Ballenas is about 7 minutes away on foot, crossing the complex garden where the shared pool is. Shops, supermarkets and banks are 10-12 minutes away.',
                ],
                'telefono' => '+1 849 382 2222',
                'whatsapp' => '+18493822222',
                'email' => 'reservations@riberamar.com',
                'publicada' => true,
                'orden' => 1,
            ]
        );

        // 1,829 sq ft declarados en las fichas de los penthouses = 169.92 m².
        $unidades = [
            [
                'slug' => 'penthouse-a3',
                'nombre' => 'Penthouse A3',
                'tipo' => 'penthouse',
                'titular' => [
                    'es' => 'Penthouse con vista al mar, terraza en la azotea y jacuzzi',
                    'en' => 'Beachview penthouse with rooftop terrace and jacuzzi',
                ],
                'capacidad_huespedes' => 6,
                'dormitorios' => 2,
                'banos_completos' => 2,
                'superficie_m2' => 169.92,
                'cargo_extra_noche' => 20.00,
                'cargo_extra_concepto' => [
                    'es' => 'Aire central en sala y comedor',
                    'en' => 'Central air in living and dining areas',
                ],
                'camas' => [['Dormitorio 1', 'Bedroom 1', 'king', 1], ['Dormitorio 2', 'Bedroom 2', 'queen', 1], ['Sala', 'Living room', 'sofa_cama', 1]],
                'amenidades' => ['terraza-techo', 'jacuzzi', 'vista-mar', 'area-picnic', 'bbq'],
            ],
            [
                'slug' => 'penthouse-b3',
                'nombre' => 'Penthouse B3',
                'tipo' => 'penthouse',
                'titular' => [
                    'es' => 'Penthouse con vista al mar, terraza en la azotea y jacuzzi',
                    'en' => 'Beachview penthouse with rooftop terrace and jacuzzi',
                ],
                'capacidad_huespedes' => 6,
                'dormitorios' => 2,
                'banos_completos' => 2,
                'superficie_m2' => 169.92,
                'cargo_extra_noche' => 20.00,
                'cargo_extra_concepto' => [
                    'es' => 'Aire central en sala y comedor',
                    'en' => 'Central air in living and dining areas',
                ],
                'camas' => [['Dormitorio 1', 'Bedroom 1', 'king', 1], ['Dormitorio 2', 'Bedroom 2', 'queen', 1], ['Sala', 'Living room', 'sofa_cama', 1]],
                'amenidades' => ['terraza-techo', 'jacuzzi', 'vista-mar', 'area-picnic', 'bbq'],
            ],
            [
                'slug' => 'duplex-a2',
                'nombre' => 'Duplex A2',
                'tipo' => 'duplex',
                'titular' => [
                    'es' => 'Dúplex con vista al mar, piscina privada y jacuzzi',
                    'en' => 'Beachview duplex with private pool and jacuzzi',
                ],
                'capacidad_huespedes' => 8,
                'dormitorios' => 3,
                'banos_completos' => 3,
                'banos_medios' => 1,
                'camas' => [['Dormitorio 1', 'Bedroom 1', 'king', 1], ['Dormitorio 2', 'Bedroom 2', 'queen', 1], ['Dormitorio 3', 'Bedroom 3', 'individual', 2], ['Sala', 'Living room', 'sofa_cama', 1]],
                'amenidades' => ['piscina-privada', 'jacuzzi', 'vista-mar', 'terraza', 'bbq'],
            ],
            [
                'slug' => 'duplex-b2',
                'nombre' => 'Duplex B2',
                'tipo' => 'duplex',
                'titular' => [
                    'es' => 'Dúplex con vista al mar, piscina privada y jacuzzi',
                    'en' => 'Beachview duplex with private pool and jacuzzi',
                ],
                'capacidad_huespedes' => 8,
                'dormitorios' => 3,
                'banos_completos' => 3,
                'banos_medios' => 1,
                'camas' => [['Dormitorio 1', 'Bedroom 1', 'king', 1], ['Dormitorio 2', 'Bedroom 2', 'queen', 1], ['Dormitorio 3', 'Bedroom 3', 'individual', 2], ['Sala', 'Living room', 'sofa_cama', 1]],
                'amenidades' => ['piscina-privada', 'jacuzzi', 'vista-mar', 'terraza', 'bbq'],
            ],
            [
                'slug' => 'condo',
                'nombre' => 'Condo',
                'tipo' => 'condo',
                'titular' => [
                    'es' => 'Apartamento de lujo frente al mar con desayuno de chef',
                    'en' => 'Beach side luxury apartment with complimentary chef breakfast',
                ],
                'capacidad_huespedes' => 6,
                'dormitorios' => 2,
                'banos_completos' => 2,
                'camas' => [['Dormitorio 1', 'Bedroom 1', 'king', 1], ['Dormitorio 2', 'Bedroom 2', 'individual', 2]],
                'amenidades' => ['jardin-privado', 'jacuzzi', 'area-picnic', 'bbq', 'generador'],
            ],
        ];

        // Amenidades que comparten las cinco unidades, según las fichas actuales.
        $comunes = [
            'aire-dormitorios', 'ventilador-techo', 'cocina-equipada', 'comedor',
            'wifi', 'tv-plasma', 'cable-satelital', 'home-theater',
            'seguridad-24h', 'caja-fuerte', 'limpieza-diaria', 'conserje',
            'lavadora', 'tendedero', 'electricidad-incluida', 'piscina-comun',
        ];

        foreach ($unidades as $orden => $datos) {
            $camas = $datos['camas'];
            $slugsAmenidades = array_merge($comunes, $datos['amenidades']);
            unset($datos['camas'], $datos['amenidades']);

            $departamento = Departamento::withoutGlobalScopes()->updateOrCreate(
                ['slug' => $datos['slug']],
                $datos + [
                    'edificio_id' => $villa->id,
                    'propietario_id' => $propietario->id,
                    'moneda' => 'USD',
                    'noches_minimas' => 2,
                    'check_in_desde' => '15:00:00',
                    'check_out_hasta' => '12:00:00',
                    // El FAQ indica flexibilidad para mascotas pequeñas y entrenadas,
                    // con depósito de seguridad por daños.
                    'mascotas_permitidas' => true,
                    'mascotas_condiciones' => [
                        'es' => 'Se aceptan mascotas pequeñas y entrenadas, con depósito de seguridad por posibles daños.',
                        'en' => 'Small, trained pets are accepted with a security deposit for possible damages.',
                    ],
                    'hora_silencio' => '23:00:00',
                    'reglas_adicionales' => [
                        'es' => 'Por respeto a los demás huéspedes, la música debe mantenerse a un volumen moderado; después de las 23:00 debe permitir una conversación normal.',
                        'en' => 'Out of respect for other guests, music should stay at a decent level; after 11:00 PM it should allow normal conversation.',
                    ],
                    'publicado' => true,
                    'orden' => $orden + 1,
                ]
            );

            $departamento->camas()->delete();
            foreach ($camas as $ordenCama => [$ambienteEs, $ambienteEn, $tipo, $cantidad]) {
                Cama::create([
                    'departamento_id' => $departamento->id,
                    'ambiente' => ['es' => $ambienteEs, 'en' => $ambienteEn],
                    'tipo' => $tipo,
                    'cantidad' => $cantidad,
                    'orden' => $ordenCama,
                ]);
            }

            $departamento->amenidades()->sync(
                Amenidad::whereIn('slug', $slugsAmenidades)->pluck('id')
            );

            // El desayuno de chef es cortesía solo en las unidades de Riberamar.
            $departamento->servicios()->sync([
                Servicio::where('slug', 'chef-desayuno')->value('id') => ['incluido' => true],
                Servicio::where('slug', 'chef-comida')->value('id') => ['incluido' => false],
                Servicio::where('slug', 'carrito-golf')->value('id') => ['incluido' => false],
                Servicio::where('slug', 'cuatrimoto')->value('id') => ['incluido' => false],
                Servicio::where('slug', 'descuento-restaurante')->value('id') => ['incluido' => true],
            ]);
        }

        $this->cargarResenas();
    }

    /**
     * Reseñas reales publicadas hoy en el sitio de WordPress.
     *
     * Quedan sin `departamento_id` porque el sitio actual las asocia al tipo de
     * unidad ("BEACHVIEW DUPLEX") y no a la unidad concreta: no se puede saber si
     * corresponden al A2 o al B2. El cliente las asignará desde el panel.
     *
     * Se omite la reseña de Mari, que corresponde a Villa Blu, fuera del alcance.
     */
    private function cargarResenas(): void
    {
        $resenas = [
            ['Indira', 'Simply amazing from the host to the person that greets to the cleaning service highly Recommend that you try this place truly unbelievable'],
            ['Katiuska', 'We had a wonderful time! Maria in concierge was very friendly, knowledgeable and accommodating! The duplex also exceeded our expectations and the location is close distance to beach and restaurants. Everything was perfect, we will come again soon'],
            ['Santiago Rafael', 'It was a magnificent experience! Excellent attention, a neat and sanitized space, as well as beautiful and comfortable.'],
            ['Cibelys', 'Everything was absolutely wonderful, the walk to the beach through the private property, the outstanding concierge and the entire villa immaculately kept. I will definitely be back!'],
            ['Deanna', 'Beautiful home, very clean, concierge service was amazing. Staff was very nice. Chef was great. Great location. Can\'t wait to go back'],
        ];

        foreach ($resenas as $orden => [$autor, $comentario]) {
            Resena::updateOrCreate(
                ['autor' => $autor],
                [
                    'comentario' => $comentario,
                    'idioma' => 'en',
                    'calificacion' => 5,
                    'publicada' => true,
                    'orden' => $orden,
                ]
            );
        }
    }
}
