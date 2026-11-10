import { version as reactVersion } from 'react';
import { version as reactDomVersion } from 'react-dom';

import themePackage from '../package.json';

/**
 * The tests must run on the same React as the site visitors get. The repository root
 * also holds bars2013's older React, and a test setup that picks it up would pass tests
 * that fail on the live site.
 */
describe('test environment', () => {
  const shippedMajor = themePackage.devDependencies.react.replace(/^\D*/, '').split('.')[0];

  it('runs on the React major version that bars2026 ships', () => {
    expect(reactVersion.split('.')[0]).toBe(shippedMajor);
  });

  it('uses the same React and React DOM versions', () => {
    expect(reactDomVersion).toBe(reactVersion);
  });
});
