import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
                bunny('IBM Plex Sans Arabic', {
                    weights: [400, 500, 600, 700],
                    preload: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        // Content-Security-Policy sources cannot express IPv6 literals such as [::1],
        // so the dev server must listen on an IPv4 address the policy can allow.
        host: '127.0.0.1',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
