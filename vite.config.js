import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/messages.js',
                'resouces/js/analytics.js',
                'resources/js/userManagement.js',
                'resources/js/app-management.js'
            ],
            refresh: true,
        }),
    ],
});
