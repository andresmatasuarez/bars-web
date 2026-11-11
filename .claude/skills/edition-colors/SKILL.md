---
name: edition-colors
description: Use when a new edition's poster is ready and its two site colors need to be picked, or when the user asks to choose, change or check an edition's poster colors ("colors" in shared/editions.json).
---

# Edition colors

Each edition has two colors taken from its poster. They live in `shared/editions.json`, next to `poster`:

```json
"colors": { "linkAccent": "#5FCB7C", "badgeText": "#50F6AB" }
```

The bars2026 theme uses the colors of the **newest edition that has them** on every page, including past edition pages such as `/programacion?e=24`. A new edition added before its poster exists (for example, to open the call) keeps the previous edition's colors until its own are picked. `Editions::colors()` reads them and `header.php` writes them into the page as CSS variables: `--color-bars-link-accent` and `--color-bars-edition-accent` get color 1, `--color-bars-badge-text` and `--color-bars-edition-glow` get color 2. The defaults live in `themes/bars2026/css/main.css`.

| Key                    | Default when no edition has colors (`main.css`)                                                 | Where it shows                                                                                                                                                                                                         |
| ---------------------- | ----------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `linkAccent` (color 1) | gold `#d4a574` (red `#8b0000` for the accents: menu line, edition number, home badge and icons) | links, "← Volver…" links, notes with a side line, the line under the active menu item, the edition number in "BARS XXVII" in the header, the home page "EDICIÓN … • year" badge and the edition, dates and venue icons |
| `badgeText` (color 2)  | coral `#d4726a` (red glow on the home poster, no glow on inner pages)                           | dates, titles on hover, small inline links, jury section names, glow at the top of inner pages and behind the home poster                                                                                              |

The red `#8b0000`, the black backgrounds and the white text never change. Buttons stay red.

## How to pick the colors

Pick by looking at the poster. Do not write a script that finds the most common colors: the largest areas of a poster are usually haze, black or the festival red, and those are the wrong choices.

1. **Look at the poster.** Make a small copy first if it is large: `magick poster.jpg -resize 480x small.jpg` (in the scratchpad). Find what makes this poster different from other years: a glow, a light, a costume, the paper.
2. **Choose two colors from the same color family.** They should sit close together on the color wheel (about 40° of hue apart or less). Opposite colors, such as blue with orange, look wrong on the site.
3. **Take the values from the poster itself.** To list the main colors of one area: `magick poster.jpg -crop WxH+X+Y +repage +dither -colors 5 -format %c histogram:info:-`.
4. **Make them easy to read.** Each color needs a contrast of at least 4.5:1 on `#1a1a1a`, the card background. If a poster color is too dark, keep its hue and make it lighter. Color 1 reads best at about 6:1 or more.
5. **Keep the year different.** Compare with the colors of the last few editions (table below and `editions.json`). Avoid the festival red unless the poster is almost all red, as in 2024.
6. **Show the user before saving.** Make a mockup of a dark page with the menu line, links, dates and a title on hover in the new colors, next to the poster. Include the previous edition for comparison. The user decides.
7. **Save and check.** Add `colors` after `poster` (or `posterSmall`) in `shared/editions.json`, run `npx prettier --write shared/editions.json`, then the unit tests (`npm run test:bars2026`). `shared/ts/editionColors.test.ts` checks the format and the contrast of every edition.

## Colors so far

| Year | Color 1 (`linkAccent`)                          | Color 2 (`badgeText`)                    |
| ---- | ----------------------------------------------- | ---------------------------------------- |
| 2022 | `#D4B45E` dust ochre (dust and road sign)       | `#A6BC7E` sage (helmet glass and haze)   |
| 2023 | `#52AAEF` neon blue (poster text, robot lights) | `#8F86F0` violet (girl's shirt, shadows) |
| 2024 | `#F58997` moon rose (halo of the moon)          | `#EE4C55` moon red (the red moon)        |
| 2025 | `#D9B896` parchment (the paper)                 | `#CC7F5C` sienna (the ink drawing)       |
| 2026 | `#5FCB7C` glow green (the crowd's glow)         | `#50F6AB` mint (the "XXVII" at the top)  |

Add each new edition to this table.
