import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/landing.css',
                'resources/css/login.css',
                'resources/css/admin.css',
                'resources/css/admin/citas.css',
                'resources/js/app.js',
                'resources/js/landing.js',
                'resources/js/admin.js',
                'resources/js/admin/dashboard-charts.js',
                'resources/js/admin/pago-stripe.js',
                'resources/js/admin/citas-calendario.js',
            ],
            refresh: true,
        }),
    ],
});
