/**
 * Integration tests: edition-aware redirects + OG tag correctness
 *
 * Tests the redirect logic in page-selection.php, page-awards.php, and seo.php
 * against a running Docker WordPress instance with seed data.
 *
 * Prerequisites:
 *   - Docker containers running: docker compose up -d
 *   - Seed data imported (movies, jury, movieblocks from various editions)
 *   - bars2026 theme active and built
 *
 * Usage:
 *   node tests/integration/test-redirects.mjs
 *
 * Test groups:
 *   A: Selection page redirects (?f= param) — page-selection.php
 *   B: Awards page redirects (?j= param) — page-awards.php
 *   C: Native WP URL redirects (301) — seo.php template_redirect
 *   D: OG tag correctness — seo.php SEO functions
 */

import { execSync } from 'node:child_process';

// ---------------------------------------------------------------------------
// Config
// ---------------------------------------------------------------------------

const BASE_URL = 'http://localhost:8083';
const WP_CMD = 'docker compose exec -T wordpress wp --allow-root';

// ANSI colors
const RED = '\x1b[0;31m';
const GREEN = '\x1b[0;32m';
const YELLOW = '\x1b[1;33m';
const CYAN = '\x1b[0;36m';
const BOLD = '\x1b[1m';
const NC = '\x1b[0m';

let passCount = 0;
let failCount = 0;
let totalCount = 0;

// ---------------------------------------------------------------------------
// Helper functions
// ---------------------------------------------------------------------------

function pass(desc) {
  passCount++;
  totalCount++;
  console.log(`  ${GREEN}PASS${NC} ${desc}`);
}

function fail(desc, detail) {
  failCount++;
  totalCount++;
  console.log(`  ${RED}FAIL${NC} ${desc}`);
  if (detail) {
    console.log(`       ${RED}${detail}${NC}`);
  }
}

function skip(id, reason) {
  console.log(`  ${YELLOW}SKIP${NC} ${id}: ${reason}`);
}

function groupHeader(title) {
  console.log('');
  console.log(`${CYAN}${BOLD}── ${title} ──${NC}`);
}

/** Run a WP-CLI command via Docker. Returns trimmed stdout or "" on error. */
function wp(args) {
  try {
    return execSync(`${WP_CMD} ${args}`, {
      encoding: 'utf8',
      stdio: ['pipe', 'pipe', 'pipe'],
    }).trim();
  } catch {
    return '';
  }
}

/**
 * Discover the first slug or title for a post type + edition from WP-CLI CSV.
 * @param {string} postType - e.g. "movie", "jury", "movieblock"
 * @param {string} metaKey - e.g. "_movie_edition"
 * @param {string} edition - e.g. "bars26"
 * @param {string} field - e.g. "post_name" or "post_title"
 */
function wpFirst(postType, metaKey, edition, field) {
  const csv = wp(
    `post list --post_type=${postType} --meta_key=${metaKey} --meta_value=${edition} --fields=${field} --format=csv`,
  );
  if (!csv) return '';
  const lines = csv.split('\n');
  if (lines.length < 2) return '';
  // Remove surrounding quotes if present (WP-CLI CSV wraps titles with spaces)
  return lines[1].replace(/^"|"$/g, '');
}

/**
 * Fetch a URL without following redirects. Returns { status, location }.
 */
async function fetchHeaders(path) {
  const url = `${BASE_URL}${path}`;
  try {
    const res = await fetch(url, {
      redirect: 'manual',
      signal: AbortSignal.timeout(10000),
    });
    return {
      status: res.status,
      location: res.headers.get('location') || '',
    };
  } catch (err) {
    return { status: 0, location: '', error: err.message };
  }
}

/**
 * Fetch a URL following redirects. Returns HTML text.
 */
async function fetchHtml(path) {
  const url = `${BASE_URL}${path}`;
  const res = await fetch(url, { signal: AbortSignal.timeout(10000) });
  return res.text();
}

/**
 * Extract the content attribute from a meta property tag.
 * Returns the content string or "" if not found.
 */
function extractOgContent(html, property) {
  const re = new RegExp(`<meta property="${escapeRegex(property)}" content="([^"]*)"`);
  const m = html.match(re);
  return m ? m[1] : '';
}

/** Escape special regex characters in a string. */
function escapeRegex(str) {
  return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// ---------------------------------------------------------------------------
// Test assertions
// ---------------------------------------------------------------------------

/**
 * Test that a URL returns the expected HTTP status code and, for redirects,
 * that the Location header matches a pattern.
 */
async function testRedirect(desc, path, expectedStatus, locationPattern) {
  const { status, location, error } = await fetchHeaders(path);

  if (error) {
    fail(desc, `fetch failed for ${BASE_URL}${path}: ${error}`);
    return;
  }

  if (status !== expectedStatus) {
    fail(desc, `Expected status ${expectedStatus}, got ${status}`);
    return;
  }

  if (locationPattern) {
    if (!location) {
      fail(
        desc,
        `Expected Location header matching '${locationPattern}', but no Location header found`,
      );
      return;
    }

    if (!new RegExp(locationPattern).test(location)) {
      fail(desc, `Location '${location}' does not match pattern '${locationPattern}'`);
      return;
    }
  }

  pass(desc);
}

/**
 * Test that a URL's HTML contains a meta tag with the given property matching a pattern.
 */
async function testOg(desc, path, property, pattern) {
  let html;
  try {
    html = await fetchHtml(path);
  } catch (err) {
    fail(desc, `fetch failed for ${BASE_URL}${path}: ${err.message}`);
    return;
  }

  const content = extractOgContent(html, property);
  if (!content) {
    fail(desc, `No <meta property="${property}"> tag found`);
    return;
  }

  if (!new RegExp(pattern).test(content)) {
    fail(desc, `Content '${content}' does not match pattern '${pattern}'`);
    return;
  }

  pass(desc);
}

/**
 * Test that a URL's HTML contains a meta tag with the given property and non-empty content.
 */
async function testOgExists(desc, path, property) {
  let html;
  try {
    html = await fetchHtml(path);
  } catch (err) {
    fail(desc, `fetch failed for ${BASE_URL}${path}: ${err.message}`);
    return;
  }

  const content = extractOgContent(html, property);
  if (!content) {
    fail(desc, `No <meta property="${property}"> tag found or content is empty`);
    return;
  }

  pass(desc);
}

/**
 * Test that a URL's HTML contains a string pattern.
 */
async function testHtmlContains(desc, path, pattern) {
  let html;
  try {
    html = await fetchHtml(path);
  } catch (err) {
    fail(desc, `fetch failed for ${BASE_URL}${path}: ${err.message}`);
    return;
  }

  if (!new RegExp(pattern).test(html)) {
    fail(desc, `HTML does not contain pattern '${pattern}'`);
    return;
  }

  pass(desc);
}

/**
 * Test that a URL's HTML does NOT contain a string pattern.
 */
async function testHtmlNotContains(desc, path, pattern) {
  let html;
  try {
    html = await fetchHtml(path);
  } catch (err) {
    fail(desc, `fetch failed for ${BASE_URL}${path}: ${err.message}`);
    return;
  }

  if (new RegExp(pattern).test(html)) {
    fail(desc, `HTML unexpectedly contains pattern '${pattern}'`);
    return;
  }

  pass(desc);
}

/**
 * Test that a URL's OG tag does NOT contain a pattern.
 */
async function testOgNotContains(desc, path, property, pattern) {
  let html;
  try {
    html = await fetchHtml(path);
  } catch (err) {
    fail(desc, `fetch failed for ${BASE_URL}${path}: ${err.message}`);
    return;
  }

  const content = extractOgContent(html, property);
  if (!content) {
    fail(desc, `No <meta property="${property}"> tag found`);
    return;
  }

  if (new RegExp(pattern).test(content)) {
    fail(desc, `Content '${content}' unexpectedly contains pattern '${pattern}'`);
    return;
  }

  pass(desc);
}

// ---------------------------------------------------------------------------
// 1. Setup & slug discovery
// ---------------------------------------------------------------------------

console.log(`${BOLD}Integration tests: edition-aware redirects + OG tags${NC}`);
console.log('');
console.log('Discovering test slugs from WordPress...');

const MOVIE_CURRENT = wpFirst('movie', '_movie_edition', 'bars26', 'post_name');
const MOVIE_PAST = wpFirst('movie', '_movie_edition', 'bars25', 'post_name');
const MBLOCK_CURRENT = wpFirst('movieblock', '_movieblock_edition', 'bars26', 'post_name');
const MBLOCK_PAST = wpFirst('movieblock', '_movieblock_edition', 'bars25', 'post_name');
const JURY_CURRENT = wpFirst('jury', '_jury_edition', 'bars26', 'post_name');
const JURY_PAST = wpFirst('jury', '_jury_edition', 'bars25', 'post_name');

const MOVIE_CURRENT_TITLE = MOVIE_CURRENT
  ? wpFirst('movie', '_movie_edition', 'bars26', 'post_title')
  : '';
const MOVIE_PAST_TITLE = MOVIE_PAST
  ? wpFirst('movie', '_movie_edition', 'bars25', 'post_title')
  : '';
const JURY_PAST_NAME = JURY_PAST ? wpFirst('jury', '_jury_edition', 'bars25', 'post_title') : '';

console.log('');
console.log(`${YELLOW}Discovered slugs:${NC}`);
console.log(`  MOVIE_CURRENT:  ${MOVIE_CURRENT || '<none>'}`);
console.log(`  MOVIE_PAST:     ${MOVIE_PAST || '<none>'}`);
console.log(`  MBLOCK_CURRENT: ${MBLOCK_CURRENT || '<none>'}`);
console.log(`  MBLOCK_PAST:    ${MBLOCK_PAST || '<none>'}`);
console.log(`  JURY_CURRENT:   ${JURY_CURRENT || '<none>'}`);
console.log(`  JURY_PAST:      ${JURY_PAST || '<none>'}`);

// Validate required slugs
const missing = [];
if (!MOVIE_CURRENT) missing.push('MOVIE_CURRENT(bars26 movie)');
if (!MOVIE_PAST) missing.push('MOVIE_PAST(bars25 movie)');
if (!JURY_PAST) missing.push('JURY_PAST(bars25 jury)');

if (missing.length > 0) {
  console.log('');
  console.log(`${RED}ERROR: Missing required test data: ${missing.join(' ')}${NC}`);
  console.log(
    'Ensure Docker is running with seed data that includes movies/jury from bars25 and bars26.',
  );
  process.exit(1);
}

// Verify the site is reachable
try {
  await fetch(`${BASE_URL}/`, { signal: AbortSignal.timeout(5000) });
} catch {
  console.log('');
  console.log(`${RED}ERROR: Cannot reach ${BASE_URL}. Is Docker running?${NC}`);
  process.exit(1);
}

// ---------------------------------------------------------------------------
// 2. Test groups
// ---------------------------------------------------------------------------

// ── Group A: Selection page redirects (302) ──

groupHeader('Group A: Selection page redirects (?f= param)');

// A1: Current-edition movie — no redirect
await testRedirect(
  'A1: Current-edition movie: no redirect',
  `/programacion/?f=${MOVIE_CURRENT}`,
  200,
);

// A2: Past-edition movie without &e= — should redirect
await testRedirect(
  'A2: Past-edition movie: redirect adds &e=25',
  `/programacion/?f=${MOVIE_PAST}`,
  302,
  `f=${MOVIE_PAST}&e=25`,
);

// A3: Past-edition movie with explicit &e= — no redirect
await testRedirect(
  'A3: Past movie with explicit &e=25: no redirect',
  `/programacion/?f=${MOVIE_PAST}&e=25`,
  200,
);

// A4: Past-edition movieblock — redirect (if we have one)
if (MBLOCK_PAST) {
  await testRedirect(
    'A4: Past-edition movieblock: redirect adds &e=25',
    `/programacion/?f=${MBLOCK_PAST}`,
    302,
    `f=${MBLOCK_PAST}&e=25`,
  );
} else {
  skip('A4', 'Past-edition movieblock (no bars25 movieblocks in seed data)');
}

// A5: Nonexistent slug — no redirect (200, just renders the page)
await testRedirect('A5: Nonexistent slug: no redirect', '/programacion/?f=nonexistent-zzz', 200);

// A6: Current movie with explicit &e=26 — no redirect
await testRedirect(
  'A6: Current movie with explicit &e=26: no redirect',
  `/programacion/?f=${MOVIE_CURRENT}&e=26`,
  200,
);

// ── Group B: Awards page redirects (302) ──

groupHeader('Group B: Awards page redirects (?j= param)');

// B1: Current-edition jury — no redirect (if we have one)
if (JURY_CURRENT) {
  await testRedirect('B1: Current-edition jury: no redirect', `/premios/?j=${JURY_CURRENT}`, 200);
} else {
  skip('B1', 'Current-edition jury (no bars26 jury in seed data)');
}

// B2: Past-edition jury without &e= — should redirect
await testRedirect(
  'B2: Past-edition jury: redirect adds &e=25',
  `/premios/?j=${JURY_PAST}`,
  302,
  `j=${JURY_PAST}&e=25`,
);

// B3: Past jury with explicit &e= — no redirect
await testRedirect(
  'B3: Past jury with explicit &e=25: no redirect',
  `/premios/?j=${JURY_PAST}&e=25`,
  200,
);

// B4: Nonexistent jury slug — no redirect
await testRedirect('B4: Nonexistent jury slug: no redirect', '/premios/?j=nonexistent-zzz', 200);

// ── Group C: Native WP URL redirects (301) ──

groupHeader('Group C: Native WP URL redirects (301)');

// C1: Current movie native URL — 301 without &e=
await testRedirect(
  'C1: Current movie /movie/slug/ → 301 without &e=',
  `/movie/${MOVIE_CURRENT}/`,
  301,
  `f=${MOVIE_CURRENT}(?!.*e=)`,
);

// C2: Past movie native URL — 301 with &e=25
await testRedirect(
  'C2: Past movie /movie/slug/ → 301 with &e=25',
  `/movie/${MOVIE_PAST}/`,
  301,
  `f=${MOVIE_PAST}&e=25`,
);

// C3: Current jury native URL — 301 without &e= (if we have one)
if (JURY_CURRENT) {
  await testRedirect(
    'C3: Current jury /jury/slug/ → 301 without &e=',
    `/jury/${JURY_CURRENT}/`,
    301,
    `j=${JURY_CURRENT}(?!.*e=)`,
  );
} else {
  skip('C3', 'Current jury native redirect (no bars26 jury in seed data)');
}

// C4: Past jury native URL — 301 with &e=25
await testRedirect(
  'C4: Past jury /jury/slug/ → 301 with &e=25',
  `/jury/${JURY_PAST}/`,
  301,
  `j=${JURY_PAST}&e=25`,
);

// ── Group D: OG tags ──

groupHeader('Group D: OG tag correctness');

// D1: Current movie og:url has no &e= param
// Note: esc_url() encodes & as &#038; in HTML
await testOgNotContains(
  'D1: Current movie og:url has no &e= param',
  `/programacion/?f=${MOVIE_CURRENT}`,
  'og:url',
  'e=',
);

// D2: Past movie og:url includes &e=25
// esc_url() encodes & as &#038; in HTML attributes
await testOg(
  'D2: Past movie og:url includes e=25',
  `/programacion/?f=${MOVIE_PAST}&e=25`,
  'og:url',
  'e=25',
);

// D3: Movie has og:image (non-empty)
await testOgExists(
  'D3: Current movie has og:image',
  `/programacion/?f=${MOVIE_CURRENT}`,
  'og:image',
);

// D4: Movie og:title contains the movie's WP title
if (MOVIE_CURRENT_TITLE) {
  await testOg(
    'D4: Current movie og:title contains movie name',
    `/programacion/?f=${MOVIE_CURRENT}`,
    'og:title',
    escapeRegex(MOVIE_CURRENT_TITLE),
  );
} else {
  skip('D4', 'Movie og:title check (could not get movie title)');
}

// D5: Past-edition page has noindex
await testHtmlContains(
  'D5: Past-edition /programacion/?e=25 has noindex',
  '/programacion/?e=25',
  'noindex',
);

// D6: Base selection page: no noindex
await testHtmlNotContains('D6: Base /programacion/ has no noindex', '/programacion/', 'noindex');

// D7: Jury og:title contains jury name
if (JURY_PAST_NAME) {
  await testOg(
    'D7: Past jury og:title contains jury name',
    `/premios/?j=${JURY_PAST}&e=25`,
    'og:title',
    escapeRegex(JURY_PAST_NAME),
  );
} else {
  skip('D7', 'Jury og:title check (could not get jury name)');
}

// D8: Past movie og:title includes edition prefix (BARS XXV)
if (MOVIE_PAST) {
  await testOg(
    'D8: Past movie og:title includes BARS XXV',
    `/programacion/?f=${MOVIE_PAST}&e=25`,
    'og:title',
    'BARS XXV',
  );
}

// ---------------------------------------------------------------------------
// 3. Summary
// ---------------------------------------------------------------------------

console.log('');
console.log(`${BOLD}────────────────────────────────${NC}`);
console.log(
  `${BOLD}Results:${NC} ${GREEN}${passCount} passed${NC}, ${RED}${failCount} failed${NC}, ${totalCount} total`,
);
console.log(`${BOLD}────────────────────────────────${NC}`);

process.exit(failCount > 0 ? 1 : 0);
