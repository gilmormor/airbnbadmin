import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            // app.* es el panel de administración (AdminLTE);
            // web.* es el sitio público (Tailwind). Se compilan por separado para
            // que el huésped no descargue el CSS del backoffice.
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/web.css',
                'resources/js/web.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
