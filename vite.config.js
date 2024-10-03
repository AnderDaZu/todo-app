import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import dotenv from 'dotenv';

// Cargar las variables de entorno
dotenv.config();

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    define: {
        'process.env': {
            APP_ENV: process.env.APP_ENV,
        },
    },
    build: {
        outDir: 'public/build',
    },
    // server: {
    //     https: true
    // }
});
