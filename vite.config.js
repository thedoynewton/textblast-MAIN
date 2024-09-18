import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 

                // General Blade (Welcome, Admin & Subadmin)
                'resources/js/app.js',
                'resources/js/redirect.js', // Welcome
                'resources/js/theme.js', // Admin

                // Message Page
                'resources/js/messages.js',
                'resources/js/messagesWarning.js',

                // Analytics
                'resources/js/analytics.js',

                // User Management
                'resources/js/userManagement.js',

                // App Management
                'resources/js/app-management.js',
                'resources/js/searchMessageLogs.js',
                'resources/js/modal.js'
            ],
            refresh: true,
        }),
    ],
});
