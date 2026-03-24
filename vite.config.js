import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/auth.css',
                'resources/js/app.jsx',
                'resources/js/old_app.js',
                'resources/sass/app.scss',
                'resources/js/delivery/products.js',
                'resources/js/delivery/orders.js',
                'resources/js/delivery/customers.js',
                'resources/js/delivery/home.js',
                'resources/js/delivery/payments.js',
                'resources/js/delivery/expenses.js',

                'resources/js/Pages/ShoppingList/Index.jsx'
            ],
            refresh: true,
        }),
        react(),
    ],
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost'
        },
        watch: {
            usePolling: true,
        }
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            '@/': '/resources/js/',
        },
    },
    optimizeDeps: {
        include: ['bootstrap', 'alpinejs'],
    },
});
