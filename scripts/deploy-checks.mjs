/**
 * Safety checks that run before a deploy (scripts/deploy.mjs).
 *
 * A deploy uploads whatever the local build folders hold, so it must only run from a
 * state that can be rebuilt exactly: the master branch, no local changes, and the
 * packages that master's package-lock.json asks for. Installed packages do not change
 * when you switch branches; building master's code with another branch's packages
 * (for example a newer Vite) can produce a broken site.
 */

export const MAIN_BRANCH = 'master';

/**
 * Compares the packages npm says are installed (node_modules/.package-lock.json, written
 * by `npm install` / `npm ci`) with package-lock.json. Returns one line per difference.
 * Optional packages that are not installed (builds for other platforms) are fine.
 */
export function findPackageMismatches(lockfile, installedLockfile) {
  if (!installedLockfile) {
    return ['node_modules/.package-lock.json is missing: the packages are not installed'];
  }

  const wanted = lockfile.packages || {};
  const installed = installedLockfile.packages || {};
  const mismatches = [];

  for (const [location, info] of Object.entries(installed)) {
    const expected = wanted[location];
    if (!expected) {
      mismatches.push(`${location}: installed ${info.version}, not in the lockfile`);
    } else if (expected.version !== info.version) {
      mismatches.push(`${location}: installed ${info.version}, lockfile ${expected.version}`);
    }
  }

  for (const [location, info] of Object.entries(wanted)) {
    if (location === '' || info.optional || installed[location]) continue;
    mismatches.push(
      info.version
        ? `${location}: in the lockfile (${info.version}), not installed`
        : `${location}: in the lockfile, not installed`,
    );
  }

  return mismatches;
}

/**
 * Returns the reasons why it is not safe to deploy now (an empty array means safe).
 *
 * @param {object} state
 * @param {string} state.branch - current git branch
 * @param {string[]} state.changedFiles - `git status --porcelain` lines
 * @param {object} state.lockfile - parsed package-lock.json
 * @param {object|null} state.installedLockfile - parsed node_modules/.package-lock.json
 */
export function findDeployProblems({ branch, changedFiles, lockfile, installedLockfile }) {
  const problems = [];

  if (branch !== MAIN_BRANCH) {
    problems.push(
      `You are on branch "${branch}". Deploys go out from ${MAIN_BRANCH}: ` +
        `merge your work, then run \`git switch ${MAIN_BRANCH}\`.`,
    );
  }

  if (changedFiles.length > 0) {
    const shown = changedFiles.slice(0, 10).map((line) => `    ${line}`);
    if (changedFiles.length > 10) shown.push(`    … and ${changedFiles.length - 10} more`);
    problems.push(
      `There are uncommitted or untracked files. Commit or stash them first:\n${shown.join('\n')}`,
    );
  }

  const mismatches = findPackageMismatches(lockfile, installedLockfile);
  if (mismatches.length > 0) {
    const shown = mismatches.slice(0, 10).map((line) => `    ${line}`);
    if (mismatches.length > 10) shown.push(`    … and ${mismatches.length - 10} more`);
    problems.push(
      `The installed packages do not match package-lock.json. Run \`npm ci\` first:\n${shown.join('\n')}`,
    );
  }

  return problems;
}
