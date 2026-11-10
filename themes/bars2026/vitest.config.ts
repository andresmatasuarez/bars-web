import { createRequire } from 'module';
import path from 'path';
import { defineConfig } from 'vitest/config';

const require = createRequire(path.join(__dirname, 'package.json'));

// Testing Library's ES module build. Its CommonJS build (package.json "main") is run by
// Node directly, and Node would load React from the root node_modules (bars2013's
// React 18) instead of this theme's React 19.
const testingLibraryReactEsm = path.join(
  path.dirname(require.resolve('@testing-library/react/package.json')),
  'dist/@testing-library/react.esm.js',
);

export default defineConfig({
  resolve: {
    alias: {
      '@shared': path.resolve(__dirname, '../../shared'),
      '@testing-library/react': testingLibraryReactEsm,
    },
    // Run the tests on this theme's React 19, the one the build ships, not bars2013's
    // React 18 in the root node_modules (same setting as vite/*.config.ts).
    dedupe: ['react', 'react-dom'],
  },

  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: ['./test-setup.ts'],
    include: ['../../shared/ts/**/*.test.ts', './ts/**/*.test.ts'],
    server: {
      deps: {
        // Process Testing Library through Vite, so its `import 'react'` follows the
        // dedupe above.
        inline: ['@testing-library/react'],
      },
    },
  },
});
