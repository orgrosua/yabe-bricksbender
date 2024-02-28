import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { v4wp } from '@kucrut/vite-for-wp';
import { wp_scripts } from '@kucrut/vite-for-wp/plugins';

export default defineConfig({
    build: {
        target: 'esnext'
    },
    plugins: [
        v4wp({
            input: {
                bricks: 'assets/bricks/main.js',
                admin: 'assets/admin/main.js',
            },
            outDir: 'build',
        }),
        wp_scripts(),
        vue(),
        {
            name: 'override-config',
            config: () => ({
                build: {
                    // ensure that manifest.json is not in ".vite/" folder
                    manifest: 'manifest.json',
                },
            }),
        }
    ],
    css: {
        lightningcss: true,
    }
});