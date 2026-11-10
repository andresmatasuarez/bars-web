import inject from '@rollup/plugin-inject';
import path from 'path';
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
  resolve: {
    alias: {
      '@shared': path.resolve(__dirname, '../../../shared'),
    },
  },
  build: {
    chunkSizeWarningLimit: 1024,
    emptyOutDir: false,
    sourcemap: true,
    outDir: '../../wp-themes/bars2013',

    lib: {
      name: 'bars',
      entry: './assets/vite-index.ts',
      fileName: 'bars',
      formats: ['iife'],
    },

    rolldownOptions: {
      output: {
        // Start the file with "use strict", as builds before Vite 8 did
        strict: true,
        // The compiled LESS must be style.css: it carries the theme header that WordPress
        // reads, and header.php links to it. Vite 8 would name it bars.css.
        assetFileNames: (assetInfo) => {
          if (assetInfo.names.some((name) => name.endsWith('.css'))) {
            return 'style.css';
          }
          return 'assets/[name][extname]';
        },
      },
    },
  },
});
