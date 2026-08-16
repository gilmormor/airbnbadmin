# Contenido extraído de riberamar.com (WordPress)

Extraído el 15 de agosto de 2026 vía la API REST de WordPress (`/wp-json/wp/v2`).
Sitio origen: WordPress 6.7.2 + tema Astra + Elementor + plugin de reservas HBook.

## Estructura

| Carpeta | Contenido |
|---|---|
| `paginas/` | 27 páginas en texto limpio (Markdown) |
| `alojamientos/` | 9 fichas del tipo `hb_accommodation` (plugin HBook) |
| `imagenes.csv` | Catálogo de 331 imágenes con URL, dimensiones y texto alternativo |
| `_raw/` | JSON original sin procesar, por si hace falta algún campo no extraído |

## Hallazgo importante: son 9 unidades, no 5

El sitio actual comercializa **nueve alojamientos**, mientras que en la reunión
se habló de cinco departamentos en Villa Riberamar. La distribución aparente es:

**Villa Riberamar (5 unidades)** — coincide con lo dicho en la reunión
- Penthouse A3 — `RIBERAMAR 5* BEACHVIEW PH W/ROOFTOP TERRA, JACUZZI A3`
- Penthouse B3 — `RIBERAMAR 5* BEACHVIEW PH W/ROOFTOP TERRA, JACUZZI B3`
- Duplex A2 — `RIBERAMAR 5* BEACHVIEW DPLX W/ PVT POOL & JACUZZI A2`
- Duplex B2 — `RIBERAMAR 5* BEACHVIEW DPLX W/ PVT POOL & JACUZZI B2`
- Condo — `BEACH SIDE LUXURY APT/COMP.CHEF SRVC BKFAST (CONDO)`

**Otras propiedades (4 unidades)** — probablemente las "otras villas" mencionadas
- Aquamare 2BR — `AQUAMARE 2BR BEACH PARADISE PREMIUM CONDO`
- Aquamare 3BR — `AQUAMARE 3BR SUNKISSED BEACH PARADISE LUXURY CONDO`
- Villa Playa
- Villa Blu

**Pendiente de confirmar con el cliente:** si estas cuatro entran en el alcance
o quedan fuera por ahora.

## Datos operativos rescatados del FAQ

Información útil que ya estaba documentada y sirve para poblar el sistema:

- **Ubicación**: complejo Bonita Village, seguridad 24 horas
- **Playa**: Playa Las Ballenas a ~7 minutos a pie, cruzando el jardín del complejo
- **Piscina común**: horario de 7:00 a 19:00
- **Jacuzzis**: todos con calentador. Las piscinas de los duplex NO son climatizadas
- **Electricidad**: incluida. Excepción: en los penthouses, el aire central de sala
  y comedor tiene un cargo adicional de **US$20 por noche**
- **Mascotas**: permitidas con flexibilidad para mascotas pequeñas y entrenadas,
  con **depósito de seguridad** por daños
- **Lavandería**: todas las unidades tienen lavadora y tendedero
- **Llaves**: entregadas en persona por el conserje a la llegada
- **Ruido**: música a volumen moderado; después de las 23:00 a nivel de conversación
- **Limpieza**: servicio diario incluido, sin costo adicional
- **Generador eléctrico**: disponible en el Condo ante cortes de luz
- **Alquiler**: carritos de golf y cuatrimotos disponibles
- **Cancelación**: por fuerza mayor se pueden mover las fechas dentro de los 45 días
  siguientes a la reserva original
- **Comercios**: supermercados, bancos y restaurantes a 10-12 minutos

## Inconsistencias detectadas

1. **Precio del servicio de chef.** La portada anuncia desde **US$45** (grupos de 6 a 8)
   y **US$165** (grupos hasta 14, US$55 por comida). El FAQ y las fichas de cada unidad
   dicen **US$35** por comida. Hay que definir el precio real antes de cargarlo.

2. **Desayuno de chef.** El FAQ dice que es cortesía, pero solo en las propiedades
   Ribera Mar, no en Aquamare, Villa Playa ni Villa Blu.

3. **Idiomas mezclados.** Existen páginas duplicadas por idioma (`inicio-english`,
   `inicio-espanol`, `riberamar-es`) pero el contenido de las fichas está solo en inglés.
   Confirma lo señalado en la reunión sobre la mezcla de idiomas.

## Datos por unidad disponibles hoy

Las fichas actuales traen, de forma irregular y en texto libre:
capacidad de huéspedes, número de dormitorios y camas, superficie en pies cuadrados
(solo en algunas), amenidades en prosa, y el precio del servicio de chef.

**No traen**: precio por noche, tarifa de limpieza, tarifa de lavandería,
depósito de seguridad, política de mascotas por unidad, ni superficie en metros cuadrados.
Todo eso hay que capturarlo desde cero en el nuevo sistema.

## Reseñas de huéspedes

Seis reseñas reales, disponibles en `paginas/inicio.md` y `paginas/riberamar-es.md`,
de: Indira, Katiuska, Santiago Rafael, Mari, Cibelys y Deanna.
Cada una está asociada a la unidad que ocupó.

## Sobre las imágenes

De las 331 imágenes catalogadas, **218 tienen 1200px de ancho o más**, por lo que
sirven para la primera versión del sitio. Aun así, conviene pedir los originales
en alta resolución antes de publicar, porque WordPress las recomprime.

Las URLs están en `imagenes.csv` y se pueden descargar en lote cuando haga falta.
