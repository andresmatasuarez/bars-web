import assert from 'node:assert/strict';
import { describe, it } from 'node:test';

import { findDeployProblems, findPackageMismatches } from './deploy-checks.mjs';

const lockfile = {
  packages: {
    '': { name: 'bars-web' },
    'node_modules/vite': { version: '4.5.14' },
    'node_modules/react': { version: '18.3.1' },
    'node_modules/@rollup/rollup-darwin-arm64': { version: '4.59.0', optional: true },
    'node_modules/bars2026': { resolved: 'themes/bars2026', link: true },
  },
};

const installedLike = (packages) => ({ packages });

const matchingInstall = installedLike({
  'node_modules/vite': { version: '4.5.14' },
  'node_modules/react': { version: '18.3.1' },
  'node_modules/bars2026': { resolved: 'themes/bars2026', link: true },
});

describe('findPackageMismatches', () => {
  it('finds nothing when the installed packages match the lockfile', () => {
    assert.deepEqual(findPackageMismatches(lockfile, matchingInstall), []);
  });

  it('ignores optional packages for other platforms that are not installed', () => {
    assert.deepEqual(findPackageMismatches(lockfile, matchingInstall), []);
  });

  it('reports a package installed in another version', () => {
    const installed = installedLike({
      ...matchingInstall.packages,
      'node_modules/vite': { version: '8.3.7' },
    });
    assert.deepEqual(findPackageMismatches(lockfile, installed), [
      'node_modules/vite: installed 8.3.7, lockfile 4.5.14',
    ]);
  });

  it('reports a package that is installed but not in the lockfile', () => {
    const installed = installedLike({
      ...matchingInstall.packages,
      'themes/bars2026/node_modules/vite': { version: '8.3.7' },
    });
    assert.deepEqual(findPackageMismatches(lockfile, installed), [
      'themes/bars2026/node_modules/vite: installed 8.3.7, not in the lockfile',
    ]);
  });

  it('reports a required package that is missing', () => {
    const installed = installedLike({ 'node_modules/vite': { version: '4.5.14' } });
    assert.deepEqual(findPackageMismatches(lockfile, installed), [
      'node_modules/react: in the lockfile (18.3.1), not installed',
      'node_modules/bars2026: in the lockfile, not installed',
    ]);
  });

  it('reports when nothing is installed', () => {
    assert.deepEqual(findPackageMismatches(lockfile, null), [
      'node_modules/.package-lock.json is missing: the packages are not installed',
    ]);
  });
});

describe('findDeployProblems', () => {
  const safeState = {
    branch: 'master',
    changedFiles: [],
    lockfile,
    installedLockfile: matchingInstall,
  };

  it('finds nothing on a clean master with matching packages', () => {
    assert.deepEqual(findDeployProblems(safeState), []);
  });

  it('refuses another branch', () => {
    const problems = findDeployProblems({ ...safeState, branch: 'update-build-tools' });
    assert.equal(problems.length, 1);
    assert.match(problems[0], /branch "update-build-tools"/);
    assert.match(problems[0], /git switch master/);
  });

  it('refuses uncommitted or untracked files and lists them', () => {
    const problems = findDeployProblems({
      ...safeState,
      changedFiles: [' M shared/editions.json', '?? themes/bars2026/php/new.php'],
    });
    assert.equal(problems.length, 1);
    assert.match(problems[0], /shared\/editions\.json/);
    assert.match(problems[0], /themes\/bars2026\/php\/new\.php/);
  });

  it('refuses packages that do not match the lockfile and says how to fix it', () => {
    const problems = findDeployProblems({
      ...safeState,
      installedLockfile: installedLike({
        ...matchingInstall.packages,
        'node_modules/vite': { version: '8.3.7' },
      }),
    });
    assert.equal(problems.length, 1);
    assert.match(problems[0], /node_modules\/vite: installed 8\.3\.7, lockfile 4\.5\.14/);
    assert.match(problems[0], /npm ci/);
  });

  it('reports every problem at once', () => {
    const problems = findDeployProblems({
      branch: 'feature',
      changedFiles: [' M a.php'],
      lockfile,
      installedLockfile: null,
    });
    assert.equal(problems.length, 3);
  });
});
