import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 

                // General Blade (Welcome, Admin & Subadmin)
                'resources/js/app.js', // Centralize Functions (Wave, Theme, Form Validation)
                'resources/js/redirect.js', // Welcome

                // Message Page
                'resources/js/messages.js',
                // Form validation is stored in app.

                // Analytics
                'resources/js/analytics.js',

                // User Management
                'resources/js/userManagement.js',

                // App Management
                'resources/js/app-management.js',
                'resources/js/messageLogs.js',
                'resources/js/modal.js'
            ],
            refresh: true,
        }),
    ],
});