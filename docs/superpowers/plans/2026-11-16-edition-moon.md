# Edition Moon Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Show each edition's own moon (cut from its poster art) in the bars2026 header and in its films' share images, starting with edition 27's splatter moon.

**Architecture:** A `moon` field per edition in `shared/editions.json` points to a ready-made PNG in `shared/resources/`. A new `Editions::moon()` helper reads it. `header.php` and `og-image.php` use it and fall back to the classic moon (`resources/bars_logo_dark.png`) when an edition has none.

**Tech Stack:** PHP 5.6 / WordPress 3.9 theme, Tailwind CSS classes in PHP templates, PHP GD for share images, ImageMagick (`magick`) for the one-off image export, Node integration tests against Docker WordPress.

**Spec:** `docs/superpowers/specs/2026-11-16-edition-moon-design.md`

## Global Constraints

- PHP must run on PHP 5.6.40 and WordPress 3.9: no `??`, no type hints, no return types, no WP 4.0+ functions.
- Change `bars2026` only. `bars2013` is archival.
- The footer and the Organization JSON-LD logo keep `resources/bars_logo_dark.png`.
- Moon PNG: `shared/resources/bars2026/bars2026_luna.png`, 305×256 px, transparent background, obelisk left see-through.
- `editions.json` field: `"moon": "resources/bars2026/bars2026_luna.png"` (path relative to the theme folder, like `poster`).
- Header classes: edition moon `h-8 w-auto lg:h-11`; classic moon keeps `w-8 h-8 lg:w-11 lg:h-11 object-contain`.
- `BARS_OG_VERSION` goes from `2` to `3`.
- Do not deploy. Do not run anything that changes the live server (`npm run og:clear:remote`, `ssh bars ...`).
- Run build, test, lint and Prettier commands through the `command-runner` agent, never in the main conversation.
- Temporary files (exports, screenshots, downloaded JPEGs) go in `.qa/`.
- Commit messages: one imperative sentence with no prefix, like the recent history ("Show HTML and blank lines in a film's comments, like the synopsis"). One logical change per commit.
- After editing a JSON, JS or MJS file, run `npx prettier --write <file>`. For existing Markdown files (`CLAUDE.md`, `shared/raw/README.md`), run `npx prettier --check <file>` and do not reformat lines you did not write.
- Integration tests need Docker running (`docker compose up -d`) and the theme built (`npm run build:bars2026`).

## Review Focus

- Phone header (375 px wide): the wider moon plus "BARS XXVII" must fit next to the menu button without wrapping or overlapping. Checked by the Task 3 visual check.
- A past edition's programme page (`/programacion/?e=26`) must show the current edition's moon in the header, the same way it keeps the current colors. Pinned by test L3 in Task 3.
- The PNG is missing from the built theme (not copied, wrong path): the header would show a broken image. Pinned by test L2 in Task 3; `og-image.php` falls back to the classic moon when the file is missing.
- A share image of a film from edition 26 or earlier must keep the classic moon next to "BARS XXVI". Checked by the Task 4 visual check.
- PHP 5.6 syntax in `editions.php`, `header.php` and `og-image.php`. Checked in Task 4 Step 6 with a search for PHP 7 syntax.

---

### Task 1: Commit the design and the plan

**Files:**

- Create: `docs/superpowers/specs/2026-11-16-edition-moon-design.md` (already written)
- Create: `docs/superpowers/plans/2026-11-16-edition-moon.md` (this file)

- [ ] **Step 1: Format both files**

Run (command-runner): `npx prettier --write docs/superpowers/specs/2026-11-16-edition-moon-design.md docs/superpowers/plans/2026-11-16-edition-moon.md`
Expected: both files listed, no errors.

- [ ] **Step 2: Commit**

```bash
git add docs/superpowers/specs/2026-11-16-edition-moon-design.md docs/superpowers/plans/2026-11-16-edition-moon.md
git commit -m "Add the design and plan for the edition moon"
```

---

### Task 2: Export edition 27's moon and keep its source

**Files:**

- Create: `shared/raw/bars2026/BARS27_LOGOchico.psd` (copied from `~/Downloads/BARS27_LOGOchico.psd`)
- Create: `shared/resources/bars2026/bars2026_luna.png`
- Modify: `shared/raw/README.md` (add a section at the end)

**Interfaces:**

- Produces: `shared/resources/bars2026/bars2026_luna.png`, served by the built theme at `resources/bars2026/bars2026_luna.png`.

- [ ] **Step 1: Copy the PSD and check its layers**

```bash
mkdir -p shared/raw/bars2026 .qa/moon
cp ~/Downloads/BARS27_LOGOchico.psd shared/raw/bars2026/
identify -format "%s %[label]\n" shared/raw/bars2026/BARS27_LOGOchico.psd 2>/dev/null
```

Expected: line `2 LUNA copy`.

- [ ] **Step 2: Export the moon**

The trimmed layer is 1298×1228. Its round part is centered at (663, 556). Cutting at y = 1112 (twice 556) centers it from top to bottom. The last 110 px fade out. Adding 28 px on the right centers it from left to right.

```bash
magick 'shared/raw/bars2026/BARS27_LOGOchico.psd[2]' -trim +repage .qa/moon/full.png
magick -size 1298x1112 xc:black -fx "clamp((1112-j)/110)" .qa/moon/fade.png
magick .qa/moon/full.png -crop 1298x1112+0+0 +repage \( .qa/moon/fade.png -alpha copy \) \
  -compose DstIn -composite -compose Over -background none -gravity west -extent 1326x1112 \
  -resize x256 -strip PNG32:shared/resources/bars2026/bars2026_luna.png
identify shared/resources/bars2026/bars2026_luna.png
```

Expected: `PNG 305x256`, about 70 KB.

- [ ] **Step 3: Check it by eye**

```bash
magick -size 400x80 xc:'#0a0a0a' \( shared/resources/bars2026/bars2026_luna.png -resize x44 \) -geometry +16+18 -composite \
  \( shared/resources/bars2026/bars2026_luna.png -resize x32 \) -geometry +120+24 -composite -scale 300% .qa/moon/check.png
```

Read `.qa/moon/check.png`. Expected: the round part is centered, the long lines below it are gone and fade out softly, no white edges, and the obelisk is visible at both sizes.

- [ ] **Step 4: Add the note to `shared/raw/README.md`**

Add a section `## Edition moon` at the end. Next year's file may be very different (no layers, a white background, other splatter), so write the goal and general steps, and give 2026 only as an example:

- Goal: a PNG of the edition's moon with the obelisk, transparent background, round part centered, 256 px tall. The site shows it at 32 and 44 px tall in the header and 53 px in share images.
- Steps: get the moon on its own (a separate layer if the file has one, otherwise remove the text and the background); make the background transparent without leaving white edges; cut splatter that pushes the round part off center, and fade the cut edges; export with `-strip` as `shared/resources/barsYYYY/barsYYYY_luna.png`; check it at 32 and 44 px on the site background (`#0a0a0a`) and in a share image; add `"moon"` to the edition in `shared/editions.json`.
- The obelisk can stay see-through: on the dark header it looks almost black.
- Example 2026: source `bars2026/BARS27_LOGOchico.psd`, layer "LUNA copy" (index 2), and the commands from Step 2.

- [ ] **Step 5: Format check**

Run (command-runner): `npx prettier --check shared/raw/README.md`
Expected: passes, or fails only on lines you did not write (leave those alone).

- [ ] **Step 6: Commit**

```bash
git add shared/raw/bars2026/BARS27_LOGOchico.psd shared/resources/bars2026/bars2026_luna.png shared/raw/README.md
git commit -m "Add edition 27's moon, cut from its logo PSD"
```

---

### Task 3: Show the current edition's moon in the header

**Files:**

- Modify: `shared/editions.json` (edition 27, after `"colors"`)
- Modify: `shared/php/editions.php` (new method after `colors()`, around line 237)
- Modify: `themes/bars2026/php/header.php:46-48`
- Test: `tests/integration/test-redirects.mjs` (new Group L before the "3. Summary" block; add `L: Edition moon (header.php)` to the group list in the top comment)

**Interfaces:**

- Consumes: `resources/bars2026/bars2026_luna.png` from Task 2.
- Produces: `Editions::moon($edition = NULL)` returns the edition's `moon` path as a string (for example `'resources/bars2026/bars2026_luna.png'`), or `null` when the edition has none. With no argument it uses `Editions::current()`.

- [ ] **Step 1: Add the `moon` field**

In `shared/editions.json`, edition 27: `"moon": "resources/bars2026/bars2026_luna.png"`, right after `"colors"`.

- [ ] **Step 2: Write the failing tests (Group L)**

Use the current edition already found in Group J (`COLORS_EDITION`): `const SITE_MOON = COLORS_EDITION.moon || null;`. Use the existing helpers `testHtmlContains`, `testJsonLd` and `escapeRegex`.

If `SITE_MOON`:

- L1 `testHtmlContains('L1: Header shows the edition ${n} moon', '/', <pattern>)`, with the pattern `src="[^"]*/${escapeRegex(SITE_MOON)}"\s+alt="BARS Logo"\s+class="h-8 w-auto lg:h-11"`.
- L2: `fetch(`${BASE_URL}/wp-content/themes/bars2026/${SITE_MOON}`)` returns status 200 and a `content-type` that starts with `image/png`. Write it inline in a `{ }` block like J11.
- L3: same pattern as L1 on `/programacion/?e=${PAST_NUM}` ("past edition page keeps the current moon").
- L4: `testHtmlContains('L4: Footer keeps the classic moon', '/', '<footer[\\s\\S]*?resources/bars_logo_dark\\.png')`.
- L5: `testJsonLd('L5: Organization logo keeps the classic moon', '/', 'Organization', (s) => /bars_logo_dark\.png$/.test(s.logo))`.

Else:

- L6: `testHtmlContains('L6: Header shows the classic moon when the edition has none', '/', '<header[\\s\\S]*?resources/bars_logo_dark\\.png[\\s\\S]*?</header>')`.

- [ ] **Step 3: Run the tests and see L1 and L3 fail**

Run (command-runner): `docker compose up -d`, then `npm run build:bars2026`, then `npm run test:integration`.
Expected: L1 and L3 FAIL (the header still shows `bars_logo_dark.png`). L2, L4, L5 and all older groups PASS.

- [ ] **Step 4: Add `Editions::moon($edition = NULL)` in `shared/php/editions.php`**

Same shape as `posterForPage()`: when `$edition` is null, use `self::current()`; return `$edition['moon']` when it is not empty, otherwise `null`. Docblock: "The edition's own moon with the obelisk, cut from its poster art ("moon" in editions.json), as a path relative to the theme folder. Returns null when the edition has none; the site then shows the classic moon."

- [ ] **Step 5: Use it in `themes/bars2026/php/header.php:46-48`**

Before the `<img>`: `<?php $edition_moon = Editions::moon(); ?>`. The `src` becomes `get_template_directory_uri() . '/' . esc_attr($edition_moon)` when there is a moon, otherwise the current `.../resources/bars_logo_dark.png`. Keep `alt="BARS Logo"`. The class is `h-8 w-auto lg:h-11` with a moon, otherwise the current `w-8 h-8 lg:w-11 lg:h-11 object-contain`. Write both class strings in full, so Tailwind finds them.

- [ ] **Step 6: Build and run the tests**

Run (command-runner): `npm run build:bars2026`, then `npm run test:integration`.
Expected: all PASS, including L1 to L5.

- [ ] **Step 7: Check the header by eye**

Use the `visual-qa` agent (or Playwright, then `mcp__playwright__browser_close`) on `http://localhost:8083/` at 1280×800 and 375×812. Screenshots go to `.qa/`. Expected: the moon is 44 px tall on desktop and 32 px on the phone, about 52 and 38 px wide; "BARS XXVII" sits next to it on one line; on the phone, the menu button is not pushed or overlapped. It should look like option E in the mockup.

- [ ] **Step 8: Format and type check**

Run (command-runner): `npx prettier --write shared/editions.json tests/integration/test-redirects.mjs`, then `git diff --stat` (only the lines you added should change), then `cd themes/bars2026 && npm run typecheck` (the TS code imports `editions.json`).
Expected: no errors.

- [ ] **Step 9: Commit**

```bash
git add shared/editions.json shared/php/editions.php themes/bars2026/php/header.php tests/integration/test-redirects.mjs
git commit -m "Show the current edition's moon in the header"
```

---

### Task 4: Use the film's edition moon in share images

**Files:**

- Modify: `themes/bars2026/php/og-image.php:33` (`BARS_OG_VERSION`) and `:376-430` (logo and edition title)

**Interfaces:**

- Consumes: `Editions::moon($edition)` from Task 3, and the existing `Editions::getByNumber()`, `Editions::current()`, `Editions::getTitle()`.

- [ ] **Step 1: Find two test films**

```bash
docker compose exec -T wordpress wp --allow-root post list --post_type=movie --meta_key=_movie_edition --meta_value=bars27 --field=post_name --posts_per_page=1
docker compose exec -T wordpress wp --allow-root post list --post_type=movie --meta_key=_movie_edition --meta_value=bars26 --field=post_name --posts_per_page=1
```

Expected: one slug each. Call them `<slug27>` and `<slug26>`.

- [ ] **Step 2: Raise the version**

`define('BARS_OG_VERSION', 3);`

- [ ] **Step 3: Pick the logo from the film's edition**

In `bars_og_generate()`, move the code that finds the film's edition (`$edition_key`, `$edition_meta`, `Editions::getByNumber()`) above the "Logo in top-left" block. Keep one variable, `$og_edition`: the film's edition when found, otherwise `Editions::current()`. Then:

- Title: `$edition_title = Editions::getTitle($og_edition);` (same result as today).
- Logo: `$moon = Editions::moon($og_edition);`. Use `get_template_directory() . '/' . $moon` when `$moon` is set and that file exists. Otherwise use the classic `resources/bars_logo_dark.png`.

Leave the logo sizing as it is: it already scales by height and places the title after the logo's width.

- [ ] **Step 4: Build, clear the local cache and get both share images**

Run (command-runner): `npm run build:bars2026`, then `npm run og:clear:local`.
Then, for each slug, get the page, read its `og:image` URL, and save the JPEG:

```bash
url=$(curl -s "http://localhost:8083/programacion/?f=<slug27>" | grep -o '<meta property="og:image" content="[^"]*"' | sed 's/.*content="//;s/"$//')
curl -s -o .qa/moon/og-27.jpg "$url"
```

Repeat for `<slug26>` into `.qa/moon/og-26.jpg`.
Expected: both URLs end in `?v=3`.

- [ ] **Step 5: Check both images by eye**

Read `.qa/moon/og-27.jpg` and `.qa/moon/og-26.jpg`. Expected: edition 27 shows the splatter moon (53 px tall, about 63 px wide) and then "BARS XXVII" with no overlap. Edition 26 shows the classic moon and then "BARS XXVI".

- [ ] **Step 6: PHP 5.6 check and integration tests**

Run: `grep -nE '\?\?|function [a-zA-Z_]+\([^)]*\b(string|int|bool|array) \$|\)\s*:\s*[a-z?]' shared/php/editions.php themes/bars2026/php/header.php themes/bars2026/php/og-image.php`
Expected: no matches in lines you wrote.
Run (command-runner): `npm run test:integration`.
Expected: all PASS (D3 still finds an `og:image`).

- [ ] **Step 7: Commit**

```bash
git add themes/bars2026/php/og-image.php
git commit -m "Use the film's edition moon in share images"
```

---

### Task 5: Document the edition moon

**Files:**

- Modify: `CLAUDE.md` (a new bullet after "Edition colors" in "Festival Data (`editions.json`)", line 165)

- [ ] **Step 1: Add the bullet**

`- **Edition moon**:` each edition can have `moon`, a PNG of that year's moon with the obelisk, cut from its poster art. bars2026 shows the current edition's moon in the header, and each film's share image shows the moon of the film's edition. Editions without one use the classic moon (`resources/bars_logo_dark.png`), which the footer and the Organization JSON-LD logo always keep. After changing a moon that share images use, raise `BARS_OG_VERSION` in `og-image.php` and clear the share image cache. How to make a moon: `shared/raw/README.md`.

- [ ] **Step 2: Format check**

Run (command-runner): `npx prettier --check CLAUDE.md`
Expected: passes, or fails only on lines you did not write (leave those alone).

- [ ] **Step 3: Commit**

```bash
git add CLAUDE.md
git commit -m "Document the edition moon in CLAUDE.md"
```

---

## After the plan (the user's steps, not part of this work)

- Deploy `bars2026` (check the live theme against the repo first, since the owner sometimes edits production by hand).
- Clear the live share image cache: `npm run og:clear:remote`.
