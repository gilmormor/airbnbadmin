# Estadía

Sistema de gestión de alquileres de corta estadía con motor de reservas propio.

Administra edificios, departamentos, propietarios y reservas; sincroniza con las OTAs
a través de Beds24; calcula comisiones de plataforma y coanfitrión; y publica un sitio
web donde el huésped consulta disponibilidad y reserva directamente.

## Un código, varios despliegues

El repositorio es el producto. Cada operación corre como un despliegue independiente,
con su propia base de datos, su propio `.env`, su propia cuenta de Beds24 y su propio
dominio. **No es multi-tenant**: una instalación sirve a una operación.

La razón es que `BEDS24_REFRESH_TOKEN` es un valor único por instalación, y un token
equivale a una cuenta de Beds24.

Despliegues actuales:

| Base de datos | Operación | Moneda |
|---|---|---|
| `adminrent` | Arriendos en Chile | CLP |
| `riberamar` | Villa Riberamar, Las Terrenas, República Dominicana | USD |

En desarrollo local se alterna cambiando `DB_DATABASE` en el `.env`.

## Dos caras, un proyecto

El mismo código sirve el sitio público y el panel, separados por prefijo de URL:

| | Ruta |
|---|---|
| Sitio público | `/` |
| Panel de administración | `/admin` |

El orden de registro en `bootstrap/app.php` importa: el panel se registra antes que
el sitio público porque la ruta `/{sucursal}/{departamento}` tiene dos segmentos y
se tragaría `/admin/reservas`. Esa ruta además restringe su primer parámetro a
`[^/]+` para que no capture barras.

El panel usa AdminLTE; el sitio público tiene su propio Tailwind. Comparten base de
datos y modelos, pero no vistas ni hojas de estilo.

## Puesta en marcha

```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
```

## Estructura del dominio

- **Edificio** — villa o conjunto. Unidad de marca del sitio público.
- **Departamento** — unidad que se alquila. Ficha completa: capacidad, superficie,
  precios, tarifas de limpieza y lavandería, reglas de la casa, amenidades y camas.
- **Acceso** — datos sensibles del departamento (clave de puerta, wifi), cifrados y
  en tabla aparte para restringirlos por permiso.
- **Reserva** — importada desde Beds24 o creada desde el sitio propio.
- **Plataforma** — Airbnb, Booking, VRBO y Directo (canal propio, sin comisión).
- **Tarifa** — precio por rango de fechas; gana la de mayor prioridad.
- **Servicio** — extras que el huésped agrega a la reserva.

Los textos visibles para el huésped son columnas JSON con una clave por idioma
(`{"es": "...", "en": "..."}`). Se leen con el método `texto()` de cada modelo, que
resuelve el idioma activo con reserva al idioma por defecto.

## Contenido inicial

`database/contenido-inicial/` guarda el contenido extraído del sitio de WordPress de
Riberamar: 27 páginas, 9 fichas de alojamiento y el catálogo de 331 imágenes. Ver el
README de esa carpeta para los hallazgos e inconsistencias detectadas.
