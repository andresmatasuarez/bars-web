import react from '@vitejs/plugin-react';
import path from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
  plugins: [react()],

  define: {
    'process.env.NODE_ENV': JSON.stringify('production'),
  },

  resolve: {
    alias: {
      '@shared': path.resolve(__dirname, '../../../shared'),
    },
    // Files in shared/ sit outside this theme, so their `import 'react'` would find the
    // root node_modules (bars2013's React 18) instead of this theme's React 19. Two
    // copies break every hook. Always use this theme's copy.
    dedupe: ['react', 'react-dom'],
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

    rolldownOptions: {
      output: {
        // Start the file with "use strict", as builds before Vite 8 did
        strict: true,
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
