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
  },

  build: {
    chunkSizeWarningLimit: 1024,
    emptyOutDir: false,
    sourcemap: true,
    minify: 'terser',
    outDir: '../../wp-themes/bars2026',

    lib: {
      name: 'selection',
      entry: './ts/react-apps/selection/index.tsx',
      fileName: 'selection',
      formats: ['iife'],
    },
  },
});
