import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    build: {
        outDir: 'public/build',
        manifest: true,
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            input: 'resources/js/app.js',
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('vue')) return 'vue'; // Vue uchun alohida bo‘lak
                        if (id.includes('bootstrap')) return 'bootstrap'; // Bootstrap uchun alohida bo‘lak
                        return 'vendor'; // Boshqa barcha kutubxonalar
                    }
                },
            }
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            // vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
