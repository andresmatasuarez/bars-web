import * as matchers from '@testing-library/jest-dom/matchers';

// Same as `import '@testing-library/jest-dom/vitest'`, but without jest-dom importing
// `vitest` itself: jest-dom sits in the root node_modules while Vitest sits in this
// theme's node_modules (it needs a newer Vite than bars2013's), so that import fails.
expect.extend(matchers);

afterEach(() => {
  localStorage.clear();
});
