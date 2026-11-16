# Edition moon

Date: 2026-11-16

## Why

Each year's poster comes with a new "moon with the obelisk" drawn in that year's theme. The festival also uses it in the spot, the printed programme and other places. In 2025 the moon looked like muscle tissue. In 2026 it is a blood splatter.

The site already takes two accent colors from each year's poster. Showing the edition's moon in place of the classic moon in a few places gives the current edition more weight. The festival owner liked the idea.

## Goals

- The header shows the current edition's moon in place of the classic moon, when the edition has one.
- The share images of film pages show the moon of the film's edition, when that edition has one.
- Edition 27 (2026) gets its moon: the splatter moon.
- The source file stays in the repo, with a note on how to make next year's moon.

## Non-goals

- The footer and the logo the site gives Google (the Organization JSON-LD in `seo.php`) keep the classic moon. They stand for the festival as a whole, not for one edition.
- `bars2013` gets no changes. It is only kept as an archive.
- Deploying, and clearing the share images saved on the live server, are not part of this work. They change the live site, so the user runs them or approves them first.

## What would make it a failure

- In the header, the moon is so small or busy that it looks like a red blob, or it shifts the header layout in a bad way.
- Share images of older films show the new moon next to "BARS XXVI".
- An edition without its own moon shows a broken image.

## Approaches

- Chosen: a `moon` field for each edition in `editions.json`, read through a new `Editions::moon()` helper. When an edition has no moon, the site shows the classic moon. This is how the poster and the colors already work. Future editions only need a new image and one line in `editions.json`, with no code changes.
- Replace `bars_logo_dark.png` with the new moon: rejected. It would also change the footer and the Google logo, and someone would have to undo it next year.
- Look for a file with a fixed name (`resources/barsYYYY/barsYYYY_luna.png`): rejected. `editions.json` is the single source of truth for edition data, and a hidden file name rule is easy to miss.

## Design

### The image

- Source: `BARS27_LOGOchico.psd`, layer "LUNA copy". It holds the moon alone, on a transparent background. The white background and the "BARS 27" text are separate layers, so nothing has to be removed.
- The obelisk stays see-through: it is a hole in the moon. On the dark header it looks almost black. In share images on bright posters it looks dark gray. Both are accepted.
- The user likes the full splatter, but the long splatter lines below the moon push the round part up. So:
  - Cut the image at the bottom, at the height where the round part ends up centered from top to bottom.
  - Fade the last stretch above the cut (about 110 px at full size), so the cut lines thin out instead of ending in a straight edge.
  - Add transparent space on the right, so the round part is also centered from left to right.
  - The result is about 6:5 (1326×1112 px at full size).
- Export a PNG 256 px tall (about 305×256) to `shared/resources/bars2026/bars2026_luna.png`. That is enough for the header (44 px tall, so 88 px on 2× screens and 132 px on 3× screens) and for the share image (53 px tall).

### Data

- `shared/editions.json`: edition 27 gets `"moon": "resources/bars2026/bars2026_luna.png"`.
- `shared/php/editions.php`: a new `Editions::moon($edition = NULL)` returns the edition's `moon` path, or `null` when it has none. Without an edition, it uses the current edition. PHP 5.6 compatible.

### Header (`themes/bars2026/php/header.php`)

- Shows `Editions::moon()` when there is one, otherwise `resources/bars_logo_dark.png`.
- The edition moon fills the full height of the logo box and grows in width (option "E"): `h-8 w-auto lg:h-11`. On desktop it is 44 px tall and about 52 px wide; on phones, 32 px tall and about 38 px wide. "BARS XXVII" moves right by the extra width. The classic moon keeps its current classes (`w-8 h-8 lg:w-11 lg:h-11`).

### Share images (`themes/bars2026/php/og-image.php`)

- Use the moon of the film's edition, which is the edition whose title the bar already shows (for example "BARS XXVI"). A film with no edition uses the current edition, the same way the title does. An edition without a moon gets the classic moon.
- The code already sizes the logo by its height and places the title after the logo's width, so the wider image fits without layout changes. The code that finds the film's edition has to run before the logo is drawn.
- Raise `BARS_OG_VERSION` from 2 to 3. This changes the image URL, so Facebook, WhatsApp and others download the new image instead of using their saved copy.
- After the deploy, the user clears the saved images on the live server (`npm run og:clear:remote`), or approves running it. To check locally: `npm run og:clear:local`.
- Known drawbacks: links people already posted keep the old image, because each platform keeps its own copy. After the cache is cleared, the first visit to each film page is a little slower, once, while its image is made again.

### Raw files

- The PSD goes to `shared/raw/bars2026/BARS27_LOGOchico.psd`.
- `shared/raw/README.md` gets a section on making an edition moon. Next year's file may be very different (no layers, a different background, different splatter), so the note gives the goal and general steps: get the moon on its own, make the background transparent, cut long splatter so the round part is centered, fade the cut edges, export, and check it at 32 and 44 px and in a share image. This year's layer name and cut sizes appear only as an example.

### Docs

- `CLAUDE.md`, "Festival Data" section: a short bullet on the `moon` field, where the site uses it, and a pointer to the note in `shared/raw/README.md`.

## Left to the builder

- The exact cut and fade numbers for the final export. Start from the mockup's numbers and check the result by eye.
- The wording of the README note and the `CLAUDE.md` bullet.
- Checks on local Docker: the header on desktop and phone, a share image for an edition 27 film, and one for an older film. Run the integration tests (`npm run test:integration`), which check that film pages have an `og:image`.
