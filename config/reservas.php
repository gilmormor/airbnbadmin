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

        // Verificado contra un export real de "Historial de transacciones" de Airbnb
        // (cuenta en español). El archivo trae varios "Tipo" de fila (Payout, Cobro como
        // coanfitrión, etc.) — el importador ignora silenciosamente cualquier fila sin
        // código de confirmación (ej. las de tipo "Payout", que son resúmenes de
        // transferencia bancaria, no reservas).
        'airbnb' => [
            'codigo_externo' => ['Código de confirmación', 'Confirmation Code', 'Confirmation code'],
            'huesped' => ['Huésped', 'Guest'],
            'listado' => ['Anuncio', 'Listing'],
            'fecha_checkin' => ['Fecha de inicio', 'Start Date'],
            'fecha_checkout' => ['Fecha de finalización', 'End Date'],
            'fecha_reserva' => ['Fecha de la reservación', 'Booked', 'Date'],
            'monto_bruto' => ['Monto', 'Amount', 'Importe'],
            'comision_plataforma' => ['Tarifa por servicio', 'Host Fee', 'Comisión de anfitrión'],
            'moneda' => ['Moneda', 'Currency'],
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
