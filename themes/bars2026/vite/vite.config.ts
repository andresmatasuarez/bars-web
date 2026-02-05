import react from '@vitejs/plugin-react';
import path from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
  plugins: [react()],

  resolve: {
    alias: {
      '@shared': path.resolve(__dirname, '../../../shared'),
    },
  },

  build: {
    chunkSizeWarningLimit: 1024,
    emptyOutDir: false,
    sourcemap: true,
    outDir: '../../wp-themes/bars2026',

    lib: {
      name: 'bars2026',
      entry: './ts/vite-index.ts',
      fileName: 'bars2026',
      formats: ['iife'],
    },

    rollupOptions: {
      output: {
        // Output CSS with predictable name
        assetFileNames: (assetInfo) => {
          if (assetInfo.name?.endsWith('.css')) {
            return 'bars2026.css';
          }
          return assetInfo.name || 'assets/[name][extname]';
        },
      },
    },
  },
});
