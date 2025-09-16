import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/auth.css',
                'resources/js/app.js',
                'resources/sass/app.scss',
                'resources/js/delivery/products.js',
                'resources/js/delivery/orders.js',
                'resources/js/delivery/customers.js',
                'resources/js/delivery/home.js',
                'resources/js/delivery/payments.js',
                'resources/js/delivery/expenses.js'
            ],
            refresh: true,
        }),
    ],
    server : {
        host: '0.0.0.0',
        hmr : {
            host: 'localhost'
        }
    }
});
