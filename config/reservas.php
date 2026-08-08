<?php

/*
 * Mapeo de columnas CSV -> campos internos de Reserva, por plataforma.
 *
 * IMPORTANTE: estos encabezados son un punto de partida basado en la documentación
 * pública de cada plataforma (no se validaron todavía contra un archivo real exportado
 * por el usuario). Deben ajustarse en cuanto se disponga de un CSV de ejemplo real:
 * el nombre exacto de las columnas puede variar según el idioma de la cuenta, el tipo
 * de reporte exportado, o cambios de la plataforma.
 *
 * Cada mapeo es case-insensitive y admite varios alias por campo (se usa el primero
 * que exista en el archivo).
 */

return [

    'mapeos' => [

        'airbnb' => [
            'codigo_externo' => ['Confirmation Code', 'Confirmation code', 'Código de confirmación'],
            'huesped' => ['Guest', 'Huésped'],
            'listado' => ['Listing', 'Anuncio'],
            'fecha_checkin' => ['Start Date', 'Fecha de llegada'],
            'fecha_checkout' => ['End Date', 'Fecha de salida'],
            'fecha_reserva' => ['Booked', 'Date', 'Fecha de reserva'],
            'monto_bruto' => ['Amount', 'Importe'],
            'comision_plataforma' => ['Host Fee', 'Comisión de anfitrión'],
            'moneda' => ['Currency', 'Moneda'],
        ],

        'booking' => [
            'codigo_externo' => ['Reservation number', 'Booking number', 'Número de reserva'],
            'huesped' => ['Guest name', 'Nombre del huésped'],
            'listado' => ['Property', 'Property name', 'Alojamiento'],
            'fecha_checkin' => ['Check-in', 'Checkin'],
            'fecha_checkout' => ['Check-out', 'Checkout'],
            'fecha_reserva' => ['Booked on', 'Booking date'],
            'monto_bruto' => ['Total amount', 'Amount'],
            'comision_plataforma' => ['Commission amount', 'Commission'],
            'moneda' => ['Currency'],
        ],

        'vrbo' => [
            'codigo_externo' => ['Reservation ID', 'Reservation external ID'],
            'huesped' => ['Guest', 'Guest name'],
            'listado' => ['Property name', 'Listing number'],
            'fecha_checkin' => ['Check-in', 'Check-in date'],
            'fecha_checkout' => ['Check-out', 'Check-out date'],
            'fecha_reserva' => ['Booking date'],
            'monto_bruto' => ['Gross sale amount', 'Rent amount'],
            'comision_plataforma' => ['Commission charge', 'Commission amount'],
            'moneda' => ['Currency'],
        ],
    ],
];
