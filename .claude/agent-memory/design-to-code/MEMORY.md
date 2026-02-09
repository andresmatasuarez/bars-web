# Design-to-Code Agent Memory

## Pencil Design File
- Location: `themes/bars2026/pencil-design/bars2026.pen`
- Desktop Convocatoria frame: `HwA2j`
- Mobile Convocatoria frame: `gGmnX`
- Desktop Content Section inner: `soXZk` (width: 1000px)
- Mobile Content Section: `uoJmD`

## Design Tokens (Pencil Variables -> Tailwind)
- `$bars-link-accent` -> `--color-bars-link-accent: #d4a574` (warm gold/copper for inline links)
  - Has alternate value `#D4726A` (rose/salmon) in Pencil, but no theme axis defined; using first value
- `$bars-badge-text` -> `--color-bars-badge-text: #d4726a` (rose/salmon for jury competition labels)
- All token mappings in: `themes/bars2026/css/main.css` under `@theme {}`

## Link Styling Pattern (Convocatoria English Note)
- Design: color=`$bars-link-accent`, fontWeight=600 (semibold), fontStyle=italic, no underline (underline on hover added for UX)
- Desktop: fontSize 14px, horizontal layout with gap-8
- Mobile: fontSize 12px, vertical layout with gap-4
- Tailwind classes: `text-bars-link-accent font-semibold italic hover:underline`

## Key Files
- CSS theme tokens: `themes/bars2026/css/main.css`
- Convocatoria page: `themes/bars2026/php/page-convocatoria.php`
- Jury modal: `themes/bars2026/ts/components/jury-modal/JuryModal.tsx`
- Modal base: `themes/bars2026/ts/components/modal/Modal.tsx`
- Tailwind v4 used (no tailwind.config.js - tokens defined in CSS `@theme {}` block)

## Jury Modal V4 Design Notes
- Desktop frame: `wZw5G`, modal container: `uP510` (600x785px, cornerRadius 16)
- Mobile frame: `NUEdH` (375px wide)
- Banner uses static halftone bg (`sala-halftone.png` at 30% opacity), NOT the member's photo
- Member photo in 260x260px circle overlaid on banner, centered horizontally at top 20px
- Competition/section text uses `$bars-badge-text` (#D4726A), NOT `$bars-primary`
- `BARS_DATA.themeUrl` available globally via `wp_localize_script` in functions.php

## Workflow Notes
- Pencil `batch_get` with `readDepth: 3` is ideal for finding text nodes inside content sections
- Variable values are fetched via `get_variables` - map `$var-name` to `--color-var-name` in Tailwind
- Always use `get_screenshot` to visually verify link/text styling from the design
