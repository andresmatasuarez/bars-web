import inject from '@rollup/plugin-inject';
import { defineConfig } from 'vite';

export default defineConfig({
  plugins: [
    // Expose jquery
    // Keep this as the first item in the plugins array
    // https://dev.to/chmich/setup-jquery-on-vite-598k
    inject({
      $: 'jquery',
      jQuery: 'jquery',
    }),
  ],
  build: {
    chunkSizeWarningLimit: 1024,
    emptyOutDir: false,
    sourcemap: true,
    outDir: './wp-content/themes/bars2013',

    lib: {
      name: 'bars',
      entry: './assets/vite-index.ts',
      fileName: 'bars',
      formats: ['iife'],
    },
  },
});
