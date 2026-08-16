<?php

namespace Database\Seeders;

use App\Models\Amenidad;
use App\Models\Cama;
use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\Empresa;
use App\Models\Propietario;
use App\Models\Resena;
use App\Models\Servicio;
use App\Models\Sucursal;
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

        $empresa = Empresa::actual();
        $empresa->update([
            'razon_social' => 'Arpel',
            'nombre_comercial' => 'Villa Ribera Mar by Arpel',
            'telefono' => '+1 849 382 2222',
            'email' => 'reservations@riberamar.com',
            'ciudad' => 'Las Terrenas',
            'pais' => 'DO',
        ]);

        $villa = Sucursal::updateOrCreate(
            ['slug' => 'villa-riberamar'],
            [
                'empresa_id' => $empresa->id,
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

        // Los nombres de las unidades (A2, A3, B2, B3) revelan dos bloques físicos.
        // El Condo es una construcción aparte dentro de la misma sucursal.
        $bloques = [];

        foreach ([['Bloque A', 3], ['Bloque B', 3], ['Condo', 1]] as $orden => [$nombre, $pisos]) {
            $bloques[$nombre] = Edificio::firstOrCreate(
                ['sucursal_id' => $villa->id, 'nombre' => $nombre],
                ['pisos' => $pisos, 'orden' => $orden]
            );
        }

        // 1,829 sq ft declarados en las fichas de los penthouses = 169.92 m².
        $unidades = [
            [
                'slug' => 'penthouse-a3',
                'bloque' => 'Bloque A',
                'piso' => 3,
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
                'bloque' => 'Bloque B',
                'piso' => 3,
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
                'bloque' => 'Bloque A',
                'piso' => 2,
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
                'bloque' => 'Bloque B',
                'piso' => 2,
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
                'bloque' => 'Condo',
                'piso' => 0,
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
            // `bloque` solo sirve para resolver el edificio; no es una columna.
            $edificioId = $bloques[$datos['bloque']]->id;
            unset($datos['camas'], $datos['amenidades'], $datos['bloque']);

            $departamento = Departamento::withoutGlobalScopes()->updateOrCreate(
                ['slug' => $datos['slug']],
                $datos + [
                    'edificio_id' => $edificioId,
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

        $this->cargarBloques();
        $this->cargarResenas();
    }

    /**
     * Secciones de texto del sitio actual de WordPress. El texto en inglés es el
     * original; el español es una traducción de trabajo que Marketing debe revisar
     * antes de publicar, según lo acordado en la reunión.
     */
    private function cargarBloques(): void
    {
        $terraza = [
            'antetitulo' => ['es' => 'Exterior', 'en' => 'Exterior'],
            'titulo' => ['es' => 'Diseño moderno minimalista', 'en' => 'Minimalist Modern Design'],
            'cuerpo' => [
                'es' => 'Relájate en un día soleado, o en una tarde de atardecer fresco con una copa de vino o nuestra tradicional cerveza «Presidente», buena música isleña, fruta recién cortada, y disfruta de la tranquilidad y la serenidad en la terraza con vista al mar, equipada con jacuzzi y área de picnic con parrilla.',
                'en' => 'Just Relax on a sunny day, or on a crisp sun set evening with a glass of wine or our traditional "Presidente" beer, some feel good island music, fresh cut fruit, and enjoy the tranquility and serenity on the ocean view terrace equipped with a Jacuzzi and "picnic area" complete with BBQ for your enjoyment.',
            ],
        ];

        $servicio = [
            'antetitulo' => ['es' => 'Nuestras habitaciones', 'en' => 'Our Room'],
            'titulo' => ['es' => 'Limpio y confortable', 'en' => 'Clean And Comfortable'],
            'cuerpo' => [
                'es' => 'Imagina todos los servicios de un hotel cinco estrellas en tu apartamento privado: sin duda un beneficio que no te puedes perder. Disfruta tu estadía sin preocupaciones porque cuentas con conserjería para reservar cenas, organizar excursiones, ayudarte con el equipaje y más, seguridad las 24 horas y limpieza diaria sin costo adicional.',
                'en' => 'Imagine all the services of a 5 star hotel in your private apartment, definitely a benefit you cannot miss. Enjoy your stay without worries because you can count on our concierge to assist with dinner reservations, book trips, help with luggage and more, security 24/7, daily cleaning services at no additional cost.',
            ],
        ];

        $presentacion = fn (string $es, string $en) => [
            'antetitulo' => ['es' => 'Nuestra propiedad', 'en' => 'Our Facility'],
            'titulo' => ['es' => 'El mejor lugar para disfrutar la vida', 'en' => 'Best place to enjoy your life'],
            'cuerpo' => ['es' => $es, 'en' => $en],
        ];

        $penthouse = $presentacion(
            'Este elegante penthouse tiene 170 m² de espacio interior donde cada rincón está decorado con un gusto impecable, con madera de roble, mármol italiano y acabados exquisitos que crean una atmósfera sublime y un ambiente romántico.',
            'This elegant Penthouse has a total of 1,829 sq ft of interior space where every corner is decorated with impeccable taste, where you can find oak wood, Italian marble and exquisite finishes that create a sublime atmosphere and a romantic mood.'
        );

        $condo = $presentacion(
            'Esta propiedad exclusiva fue diseñada para preservar la esencia de su entorno, conservando las áreas verdes y el Río Salado que desemboca en el mar, escondida en una cala apartada junto a Playa Las Ballenas.',
            'This exclusive property was designed with the intention of preserving the essence of its surroundings by conserving the green areas and the Salt River running water into the sea, tucked away in a secluded cove by Las Ballenas Beach.'
        );

        // Lista que el sitio actual muestra bajo «Your reservation includes».
        // El inglés es el original; el español es traducción de trabajo.
        $incluye = function (array $lineas, string $etiqueta) {
            return [
                'antetitulo' => ['es' => $etiqueta, 'en' => $etiqueta],
                'titulo' => [
                    'es' => 'Tu reserva incluye estas comodidades',
                    'en' => 'Your reservation includes the following amenities',
                ],
                'cuerpo' => [
                    'es' => 'Decoración moderna y contemporánea, bellamente amueblado y totalmente equipado. Todos los dormitorios tienen aire acondicionado, televisor de pantalla plana, cable y satélite, ventilador de techo, wifi, home theater y caja fuerte.',
                    'en' => 'Modern and contemporary decoration, beautifully furnished, and fully equipped. All bedrooms have air conditioners, plasma TV, cable/satellite, ceiling fan, Wi-Fi, home theater, safety deposit box.',
                ],
                'items' => array_map(fn ($linea) => ['es' => $linea[0], 'en' => $linea[1]], $lineas),
            ];
        };

        $comunesLista = [
            ['Cocina totalmente equipada, sala y comedor', 'Fully equipped Kitchen, Living room and dining room'],
            ['Servicio de limpieza diario', 'Daily cleaning service'],
            ['Electricidad incluida en estadías cortas', 'Electricity included on short stays'],
            ['Servicio de chef para almuerzo o cena, US$35 por comida (los huéspedes ponen los víveres)', 'Chef Service for lunch or dinner $35USD per meal (Guest supply the groceries)'],
        ];

        $incluyePenthouse = $incluye(array_merge([
            ['2 dormitorios (6 huéspedes), aire acondicionado en los dormitorios', '2 bedrooms (6 guests), Airconditioning in bedrooms'],
        ], [$comunesLista[0]], [
            ['Un penthouse elegante, seguridad 24 horas', 'An elegant Penthouse / 24 hr security'],
            ['Terraza en la azotea con jacuzzi', 'Roof terrace with Jacuzzi'],
        ], array_slice($comunesLista, 1)), '#PenthouseA3');

        $incluyeDuplex = $incluye(array_merge([
            ['3 dormitorios (8 huéspedes), aire acondicionado en todos los dormitorios', '3 bedrooms (8 guests), Air conditioning in all bedrooms'],
        ], [$comunesLista[0]], [
            ['Piscina privada con jacuzzi', 'Private pool with Jacuzzi'],
            ['Seguridad 24 horas', '24 hr security'],
        ], array_slice($comunesLista, 1)), '#Duplex');

        $incluyeCondo = $incluye(array_merge([
            ['2 dormitorios (6 huéspedes), 3 camas, aire acondicionado en los dormitorios', '2 bedrooms (6 guests), (3) beds, Air-conditioning in bedrooms'],
        ], [$comunesLista[0]], [
            ['Jardín privado con jacuzzi', 'Private garden with Jacuzzi'],
            ['Planta eléctrica ante cortes de luz', 'Power Generator (In the event of power outages)'],
        ], array_slice($comunesLista, 1)), '#Condo');

        $porUnidad = [
            'penthouse-a3' => [$penthouse, $terraza, $servicio, $incluyePenthouse],
            'penthouse-b3' => [$penthouse, $terraza, $servicio, $incluyePenthouse],
            'duplex-a2' => [$condo, $terraza, $servicio, $incluyeDuplex],
            'duplex-b2' => [$condo, $terraza, $servicio, $incluyeDuplex],
            'condo' => [$condo, $terraza, $servicio, $incluyeCondo],
        ];

        foreach ($porUnidad as $slug => $bloques) {
            $departamento = Departamento::withoutGlobalScopes()->where('slug', $slug)->first();

            if (! $departamento) {
                continue;
            }

            $departamento->bloques()->delete();

            foreach ($bloques as $orden => $bloque) {
                $departamento->bloques()->create($bloque + ['orden' => $orden]);
            }
        }
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
