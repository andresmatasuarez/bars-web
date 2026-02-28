/**
 * Integration tests: edition-aware redirects + SEO coverage
 *
 * Tests redirect logic in page-selection.php, page-awards.php, and seo.php,
 * plus canonical URLs, meta descriptions, JSON-LD, and other SEO tags
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
 *   E: Movieblock native URL redirects (301) — seo.php template_redirect
 *   F: Canonical URLs — seo.php
 *   G: Meta descriptions — seo.php
 *   H: JSON-LD structured data — seo.php
 *   I: Miscellaneous SEO (noindex, robots.txt, og:type) — seo.php
 */

import { execSync } from 'node:child_process';
import { readFileSync } from 'node:fs';

// ---------------------------------------------------------------------------
// Config
// ---------------------------------------------------------------------------

const BASE_URL = 'http://localhost:8083';
const WP_CMD = 'docker compose exec -T wordpress wp --allow-root';

// Dynamic edition discovery from shared/editions.json
const editions = JSON.parse(readFileSync('shared/editions.json', 'utf8'));
const CURRENT_NUM = editions[0].number;
const PAST_NUM = CURRENT_NUM - 1;
const CURRENT_ED = `bars${CURRENT_NUM}`;
const PAST_ED = `bars${PAST_NUM}`;

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

// Temporary post tracking for cleanup
const tempPostIds = [];

function cleanup() {
  for (const id of tempPostIds) {
    wp(`post delete ${id} --force`);
  }
  tempPostIds.length = 0;
}
process.on('exit', cleanup);
process.on('SIGINT', () => {
  cleanup();
  process.exit(130);
});
process.on('SIGTERM', () => {
  cleanup();
  process.exit(143);
});

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

/** Convert integer to Roman numeral. */
function toRoman(num) {
  const vals = [1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1];
  const syms = ['M', 'CM', 'D', 'CD', 'C', 'XC', 'L', 'XL', 'X', 'IX', 'V', 'IV', 'I'];
  let result = '';
  for (let i = 0; i < vals.length; i++) {
    while (num >= vals[i]) {
      result += syms[i];
      num -= vals[i];
    }
  }
  return result;
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

/** Extract <link rel="canonical" href="..."> */
function extractCanonical(html) {
  const m = html.match(/<link rel="canonical" href="([^"]*)"/);
  return m ? m[1] : '';
}

/** Extract <meta name="X" content="Y"> (for description, robots — NOT og: property tags) */
function extractMetaName(html, name) {
  const re = new RegExp(`<meta name="${escapeRegex(name)}" content="([^"]*)"`);
  const m = html.match(re);
  return m ? m[1] : '';
}

/** Parse all <script type="application/ld+json"> blocks into a flat array of schema objects */
function extractJsonLd(html) {
  const schemas = [];
  const re = /<script type="application\/ld\+json">([\s\S]*?)<\/script>/g;
  let m;
  while ((m = re.exec(html)) !== null) {
    try {
      const parsed = JSON.parse(m[1]);
      if (parsed['@graph'] && Array.isArray(parsed['@graph'])) {
        schemas.push(...parsed['@graph']);
      } else {
        schemas.push(parsed);
      }
    } catch {
      // ignore malformed JSON-LD
    }
  }
  return schemas;
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

/** Assert canonical URL ends with expected path */
async function testCanonical(desc, path, expectedSuffix) {
  let html;
  try {
    html = await fetchHtml(path);
  } catch (err) {
    fail(desc, `fetch failed for ${BASE_URL}${path}: ${err.message}`);
    return;
  }

  const canonical = extractCanonical(html);
  if (!canonical) {
    fail(desc, 'No <link rel="canonical"> tag found');
    return;
  }

  if (!canonical.endsWith(expectedSuffix)) {
    fail(desc, `Canonical '${canonical}' does not end with '${expectedSuffix}'`);
    return;
  }

  pass(desc);
}

/** Assert JSON-LD contains a schema of the given @type, optionally with extra property check */
async function testJsonLd(desc, path, expectedType, extraCheck) {
  let html;
  try {
    html = await fetchHtml(path);
  } catch (err) {
    fail(desc, `fetch failed for ${BASE_URL}${path}: ${err.message}`);
    return;
  }

  const schemas = extractJsonLd(html);
  const match = schemas.find((s) => s['@type'] === expectedType);
  if (!match) {
    const types = schemas.map((s) => s['@type']).join(', ');
    fail(desc, `No JSON-LD schema with @type "${expectedType}" found (found: ${types || 'none'})`);
    return;
  }

  if (extraCheck && !extraCheck(match)) {
    fail(desc, `JSON-LD @type "${expectedType}" found but extra check failed`);
    return;
  }

  pass(desc);
}

/** Assert <meta name="..."> exists with non-empty content, optionally matching pattern */
async function testMetaName(desc, path, name, pattern) {
  let html;
  try {
    html = await fetchHtml(path);
  } catch (err) {
    fail(desc, `fetch failed for ${BASE_URL}${path}: ${err.message}`);
    return;
  }

  const content = extractMetaName(html, name);
  if (!content) {
    fail(desc, `No <meta name="${name}"> tag found or content is empty`);
    return;
  }

  if (pattern && !new RegExp(pattern).test(content)) {
    fail(desc, `Content '${content}' does not match pattern '${pattern}'`);
    return;
  }

  pass(desc);
}

/** Fetch raw text (not HTML page) and check it contains a pattern — for robots.txt */
async function testFetchContains(desc, path, pattern) {
  let text;
  try {
    const res = await fetch(`${BASE_URL}${path}`, { signal: AbortSignal.timeout(10000) });
    text = await res.text();
  } catch (err) {
    fail(desc, `fetch failed for ${BASE_URL}${path}: ${err.message}`);
    return;
  }

  if (!new RegExp(pattern).test(text)) {
    fail(desc, `Response does not contain pattern '${pattern}'`);
    return;
  }

  pass(desc);
}

// ---------------------------------------------------------------------------
// 1. Setup & slug discovery
// ---------------------------------------------------------------------------

console.log(`${BOLD}Integration tests: edition-aware redirects + SEO${NC}`);
console.log('');
console.log(
  `Using editions: current=${CURRENT_ED} (${CURRENT_NUM}), past=${PAST_ED} (${PAST_NUM})`,
);
console.log('');
console.log('Discovering test slugs from WordPress...');

let MOVIE_CURRENT = wpFirst('movie', '_movie_edition', CURRENT_ED, 'post_name');
const MOVIE_PAST = wpFirst('movie', '_movie_edition', PAST_ED, 'post_name');
let MBLOCK_CURRENT = wpFirst('movieblock', '_movieblock_edition', CURRENT_ED, 'post_name');
const MBLOCK_PAST = wpFirst('movieblock', '_movieblock_edition', PAST_ED, 'post_name');
let JURY_CURRENT = wpFirst('jury', '_jury_edition', CURRENT_ED, 'post_name');
const JURY_PAST = wpFirst('jury', '_jury_edition', PAST_ED, 'post_name');

let MOVIE_CURRENT_TITLE = MOVIE_CURRENT
  ? wpFirst('movie', '_movie_edition', CURRENT_ED, 'post_title')
  : '';

// Auto-create temporary test posts when seed data is missing for current edition
if (!MOVIE_CURRENT) {
  console.log(`${YELLOW}No ${CURRENT_ED} movies found — creating temporary test post...${NC}`);
  const movieId = wp(
    'post create --post_type=movie --post_title="Integration Test Movie" --post_name=integration-test-movie --post_status=publish --porcelain',
  );
  if (movieId) {
    wp(`post meta update ${movieId} _movie_edition ${CURRENT_ED}`);
    wp(`post meta update ${movieId} _movie_name "Integration Test Movie"`);
    wp(`post meta update ${movieId} _movie_synopsis "A test movie for integration testing."`);
    wp(`post meta update ${movieId} _movie_section opening`);
    tempPostIds.push(movieId);
    MOVIE_CURRENT = 'integration-test-movie';
    MOVIE_CURRENT_TITLE = 'Integration Test Movie';
  }
}

if (!MBLOCK_CURRENT) {
  console.log(`${YELLOW}No ${CURRENT_ED} movieblocks found — creating temporary test post...${NC}`);
  const id = wp(
    'post create --post_type=movieblock --post_title="Integration Test Block" --post_name=integration-test-block --post_status=publish --porcelain',
  );
  if (id) {
    wp(`post meta update ${id} _movieblock_edition ${CURRENT_ED}`);
    wp(`post meta update ${id} _movieblock_name "Integration Test Block"`);
    wp(`post meta update ${id} _movieblock_section shortFilmCompetition`);
    tempPostIds.push(id);
    MBLOCK_CURRENT = 'integration-test-block';
  }
}

if (!JURY_CURRENT) {
  console.log(`${YELLOW}No ${CURRENT_ED} jury found — creating temporary test post...${NC}`);
  const id = wp(
    'post create --post_type=jury --post_title="Integration Test Juror" --post_name=integration-test-juror --post_status=publish --porcelain',
  );
  if (id) {
    wp(`post meta update ${id} _jury_edition ${CURRENT_ED}`);
    wp(`post meta update ${id} _jury_name "Integration Test Juror"`);
    wp(`post meta update ${id} _jury_section internationalFeatureFilmCompetition`);
    tempPostIds.push(id);
    JURY_CURRENT = 'integration-test-juror';
  }
}

// Discover a published blog post for SEO tests (F4, G4, H5)
const blogCsv = wp(
  'post list --post_type=post --post_status=publish --fields=ID,post_name --format=csv',
);
let BLOG_POST_SLUG = '';
let BLOG_POST_PATH = '';
if (blogCsv) {
  const lines = blogCsv.split('\n');
  if (lines.length >= 2) {
    const parts = lines[1].split(',');
    const blogId = parts[0] || '';
    BLOG_POST_SLUG = (parts[1] || '').replace(/^"|"$/g, '');
    if (blogId) {
      const permalink = wp(`eval 'echo get_permalink(${blogId});'`);
      if (permalink) {
        try {
          const url = new URL(permalink);
          // Include query string for plain-permalink setups (e.g. ?p=123)
          BLOG_POST_PATH = url.pathname + url.search;
        } catch {
          BLOG_POST_PATH = '';
        }
      }
    }
  }
}

const MOVIE_PAST_TITLE = MOVIE_PAST
  ? wpFirst('movie', '_movie_edition', PAST_ED, 'post_title')
  : '';
const JURY_PAST_NAME = JURY_PAST ? wpFirst('jury', '_jury_edition', PAST_ED, 'post_title') : '';

console.log('');
console.log(`${YELLOW}Discovered slugs:${NC}`);
console.log(`  MOVIE_CURRENT:  ${MOVIE_CURRENT || '<none>'}`);
console.log(`  MOVIE_PAST:     ${MOVIE_PAST || '<none>'}`);
console.log(`  MBLOCK_CURRENT: ${MBLOCK_CURRENT || '<none>'}`);
console.log(`  MBLOCK_PAST:    ${MBLOCK_PAST || '<none>'}`);
console.log(`  JURY_CURRENT:   ${JURY_CURRENT || '<none>'}`);
console.log(`  JURY_PAST:      ${JURY_PAST || '<none>'}`);
console.log(`  BLOG_POST:      ${BLOG_POST_SLUG || '<none>'} → ${BLOG_POST_PATH || '<none>'}`);
if (tempPostIds.length > 0) {
  console.log(`  ${YELLOW}Temporary posts created: ${tempPostIds.join(', ')}${NC}`);
}

// Validate required slugs
const missing = [];
if (!MOVIE_CURRENT) missing.push(`MOVIE_CURRENT(${CURRENT_ED} movie)`);
if (!MOVIE_PAST) missing.push(`MOVIE_PAST(${PAST_ED} movie)`);
if (!MBLOCK_CURRENT) missing.push(`MBLOCK_CURRENT(${CURRENT_ED} movieblock)`);
if (!JURY_CURRENT) missing.push(`JURY_CURRENT(${CURRENT_ED} jury)`);
if (!JURY_PAST) missing.push(`JURY_PAST(${PAST_ED} jury)`);

if (missing.length > 0) {
  console.log('');
  console.log(`${RED}ERROR: Missing required test data: ${missing.join(' ')}${NC}`);
  console.log(
    'Ensure Docker is running with seed data that includes movies/jury from multiple editions.',
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
  `A2: Past-edition movie: redirect adds &e=${PAST_NUM}`,
  `/programacion/?f=${MOVIE_PAST}`,
  302,
  `f=${MOVIE_PAST}&e=${PAST_NUM}`,
);

// A3: Past-edition movie with explicit &e= — no redirect
await testRedirect(
  `A3: Past movie with explicit &e=${PAST_NUM}: no redirect`,
  `/programacion/?f=${MOVIE_PAST}&e=${PAST_NUM}`,
  200,
);

// A4: Past-edition movieblock — redirect (if we have one)
if (MBLOCK_PAST) {
  await testRedirect(
    `A4: Past-edition movieblock: redirect adds &e=${PAST_NUM}`,
    `/programacion/?f=${MBLOCK_PAST}`,
    302,
    `f=${MBLOCK_PAST}&e=${PAST_NUM}`,
  );
} else {
  skip('A4', `Past-edition movieblock (no ${PAST_ED} movieblocks in seed data)`);
}

// A5: Nonexistent slug — no redirect (200, just renders the page)
await testRedirect('A5: Nonexistent slug: no redirect', '/programacion/?f=nonexistent-zzz', 200);

// A6: Current movie with explicit &e=N — no redirect
await testRedirect(
  `A6: Current movie with explicit &e=${CURRENT_NUM}: no redirect`,
  `/programacion/?f=${MOVIE_CURRENT}&e=${CURRENT_NUM}`,
  200,
);

// ── Group B: Awards page redirects (302) ──

groupHeader('Group B: Awards page redirects (?j= param)');

// B1: Current-edition jury — no redirect
if (JURY_CURRENT) {
  await testRedirect('B1: Current-edition jury: no redirect', `/premios/?j=${JURY_CURRENT}`, 200);
} else {
  skip('B1', `Current-edition jury (no ${CURRENT_ED} jury in seed data)`);
}

// B2: Past-edition jury without &e= — should redirect
await testRedirect(
  `B2: Past-edition jury: redirect adds &e=${PAST_NUM}`,
  `/premios/?j=${JURY_PAST}`,
  302,
  `j=${JURY_PAST}&e=${PAST_NUM}`,
);

// B3: Past jury with explicit &e= — no redirect
await testRedirect(
  `B3: Past jury with explicit &e=${PAST_NUM}: no redirect`,
  `/premios/?j=${JURY_PAST}&e=${PAST_NUM}`,
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

// C2: Past movie native URL — 301 with &e=N
await testRedirect(
  `C2: Past movie /movie/slug/ → 301 with &e=${PAST_NUM}`,
  `/movie/${MOVIE_PAST}/`,
  301,
  `f=${MOVIE_PAST}&e=${PAST_NUM}`,
);

// C3: Current jury native URL — 301 without &e=
if (JURY_CURRENT) {
  await testRedirect(
    'C3: Current jury /jury/slug/ → 301 without &e=',
    `/jury/${JURY_CURRENT}/`,
    301,
    `j=${JURY_CURRENT}(?!.*e=)`,
  );
} else {
  skip('C3', `Current jury native redirect (no ${CURRENT_ED} jury in seed data)`);
}

// C4: Past jury native URL — 301 with &e=N
await testRedirect(
  `C4: Past jury /jury/slug/ → 301 with &e=${PAST_NUM}`,
  `/jury/${JURY_PAST}/`,
  301,
  `j=${JURY_PAST}&e=${PAST_NUM}`,
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

// D2: Past movie og:url includes &e=N
// esc_url() encodes & as &#038; in HTML attributes
await testOg(
  `D2: Past movie og:url includes e=${PAST_NUM}`,
  `/programacion/?f=${MOVIE_PAST}&e=${PAST_NUM}`,
  'og:url',
  `e=${PAST_NUM}`,
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
  `D5: Past-edition /programacion/?e=${PAST_NUM} has noindex`,
  `/programacion/?e=${PAST_NUM}`,
  'noindex',
);

// D6: Base selection page: no noindex
await testHtmlNotContains('D6: Base /programacion/ has no noindex', '/programacion/', 'noindex');

// D7: Jury og:title contains jury name
if (JURY_PAST_NAME) {
  await testOg(
    'D7: Past jury og:title contains jury name',
    `/premios/?j=${JURY_PAST}&e=${PAST_NUM}`,
    'og:title',
    escapeRegex(JURY_PAST_NAME),
  );
} else {
  skip('D7', 'Jury og:title check (could not get jury name)');
}

// D8: Past movie og:title includes edition prefix (BARS <roman>)
if (MOVIE_PAST) {
  await testOg(
    `D8: Past movie og:title includes BARS ${toRoman(PAST_NUM)}`,
    `/programacion/?f=${MOVIE_PAST}&e=${PAST_NUM}`,
    'og:title',
    `BARS ${toRoman(PAST_NUM)}`,
  );
}

// ── Group E: Movieblock native URL redirects (301) ──

groupHeader('Group E: Movieblock native URL redirects (301)');

// E1: Current movieblock native URL — 301 without &e=
await testRedirect(
  'E1: Current movieblock /movieblock/slug/ → 301 without &e=',
  `/movieblock/${MBLOCK_CURRENT}/`,
  301,
  `f=${MBLOCK_CURRENT}(?!.*e=)`,
);

// E2: Past movieblock native URL — 301 with &e=N
if (MBLOCK_PAST) {
  await testRedirect(
    `E2: Past movieblock /movieblock/slug/ → 301 with &e=${PAST_NUM}`,
    `/movieblock/${MBLOCK_PAST}/`,
    301,
    `f=${MBLOCK_PAST}&e=${PAST_NUM}`,
  );
} else {
  skip('E2', `Past-edition movieblock (no ${PAST_ED} movieblocks in seed data)`);
}

// ── Group F: Canonical URLs ──

groupHeader('Group F: Canonical URLs');

// F1: Movie modal canonical = /programacion
await testCanonical(
  'F1: Movie modal canonical ends with /programacion',
  `/programacion/?f=${MOVIE_CURRENT}`,
  '/programacion',
);

// F2: Jury modal canonical = /premios
await testCanonical(
  'F2: Jury modal canonical ends with /premios',
  `/premios/?j=${JURY_CURRENT}`,
  '/premios',
);

// F3: Front page canonical = /
await testCanonical('F3: Front page canonical ends with /', '/', '/');

// F4: Blog post canonical contains post slug (requires pretty permalinks)
if (BLOG_POST_PATH && BLOG_POST_PATH.includes(BLOG_POST_SLUG)) {
  await testCanonical(
    'F4: Blog post canonical contains post slug',
    BLOG_POST_PATH,
    `/${BLOG_POST_SLUG}/`,
  );
} else {
  skip('F4', 'Blog post canonical (no published blog posts or plain permalink structure)');
}

// F5: Past-edition page canonical strips ?e=
await testCanonical(
  `F5: Past-edition /programacion?e=${PAST_NUM} canonical strips ?e=`,
  `/programacion/?e=${PAST_NUM}`,
  '/programacion/',
);

// ── Group G: Meta descriptions ──

groupHeader('Group G: Meta descriptions');

// G1: Front page description contains "Terror"
await testMetaName(
  'G1: Front page meta description contains "Terror"',
  '/',
  'description',
  'Terror',
);

// G2: Movie modal has non-empty meta description
await testMetaName(
  'G2: Movie modal has non-empty meta description',
  `/programacion/?f=${MOVIE_CURRENT}`,
  'description',
);

// G3: /programacion/ has per-slug description
await testMetaName(
  'G3: /programacion/ description contains "Programación completa"',
  '/programacion/',
  'description',
  'Programación completa',
);

// G4: Blog post has non-empty meta description
if (BLOG_POST_PATH && BLOG_POST_PATH.includes(BLOG_POST_SLUG)) {
  await testMetaName('G4: Blog post has non-empty meta description', BLOG_POST_PATH, 'description');
} else {
  skip('G4', 'Blog post meta description (no published blog posts or plain permalink structure)');
}

// ── Group H: JSON-LD structured data ──

groupHeader('Group H: JSON-LD structured data');

// H1: Front page has Organization schema
await testJsonLd('H1: Front page has Organization schema', '/', 'Organization');

// H2: Front page has Event schema with startDate
await testJsonLd('H2: Front page has Event schema with startDate', '/', 'Event', (schema) => {
  return !!schema.startDate;
});

// H3: Front page Event has physical venue(s)
await testJsonLd(
  'H3: Front page Event has physical venue with @type Place',
  '/',
  'Event',
  (schema) => {
    const loc = schema.location;
    if (!loc) return false;
    if (Array.isArray(loc)) {
      return loc.some((l) => l['@type'] === 'Place');
    }
    return loc['@type'] === 'Place';
  },
);

// H4: Movie modal has Movie schema with name
await testJsonLd(
  'H4: Movie modal has Movie schema with name',
  `/programacion/?f=${MOVIE_CURRENT}`,
  'Movie',
  (schema) => !!schema.name,
);

// H5: Blog post has NewsArticle schema
if (BLOG_POST_PATH && BLOG_POST_PATH.includes(BLOG_POST_SLUG)) {
  await testJsonLd(
    'H5: Blog post has NewsArticle schema with headline',
    BLOG_POST_PATH,
    'NewsArticle',
    (schema) => !!schema.headline,
  );
} else {
  skip('H5', 'Blog post NewsArticle schema (no published blog posts or plain permalink structure)');
}

// ── Group I: Miscellaneous SEO ──

groupHeader('Group I: Miscellaneous SEO');

// I1: Past-edition /premios has noindex
await testHtmlContains(
  `I1: /premios?e=${PAST_NUM} has noindex`,
  `/premios/?e=${PAST_NUM}`,
  'noindex',
);

// I2: Base /premios has no noindex
await testHtmlNotContains('I2: Base /premios/ has no noindex', '/premios/', 'noindex');

// I3: robots.txt has Sitemap directive
await testFetchContains('I3: robots.txt has Sitemap directive', '/robots.txt', 'wp-sitemap\\.xml');

// I4: Movie modal og:type = video.movie
await testOg(
  'I4: Movie modal og:type is video.movie',
  `/programacion/?f=${MOVIE_CURRENT}`,
  'og:type',
  '^video\\.movie$',
);

// I5: Jury modal og:type = profile
await testOg(
  'I5: Jury modal og:type is profile',
  `/premios/?j=${JURY_CURRENT}`,
  'og:type',
  '^profile$',
);

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
