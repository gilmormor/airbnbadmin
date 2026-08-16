/*
 * Punto de entrada del sitio público. Se mantiene separado de app.js, que carga
 * jQuery, Bootstrap y DataTables para el panel.
 */

/**
 * Muestra las secciones deslizándolas desde su lado al entrar en pantalla.
 *
 * El estado oculto se aplica aquí y no en la hoja de estilos a propósito: si este
 * script fallara, el contenido queda visible en lugar de desaparecer. Quien pidió
 * movimiento reducido en su sistema ve todo de una vez, sin animación.
 */
document.addEventListener('DOMContentLoaded', () => {
    const secciones = document.querySelectorAll('.revelar');

    if (secciones.length === 0) {
        return;
    }

    const sinMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (sinMovimiento || !('IntersectionObserver' in window)) {
        secciones.forEach((seccion) => seccion.classList.add('visible'));
        return;
    }

    secciones.forEach((seccion) => seccion.classList.add('preparado'));

    const observador = new IntersectionObserver(
        (entradas) => {
            entradas.forEach((entrada) => {
                if (entrada.isIntersecting) {
                    entrada.target.classList.add('visible');
                    // Una sola vez: reaparecer al volver a subir distrae.
                    observador.unobserve(entrada.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -60px 0px' }
    );

    secciones.forEach((seccion) => observador.observe(seccion));
});
