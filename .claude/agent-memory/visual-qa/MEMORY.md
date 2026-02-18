# Visual QA Memory

## Known Issues

### Hero Section Button Layout (320x568 - iPhone SE)

Issue: On very small mobile viewports (320x568), the hero section has a fixed height of 600px, causing the "Convocatoria abierta" button to be cut off below the viewport fold.

Location: `/home/amatasuarez/workspace/bars-web/themes/bars2026/php/template-parts/sections/hero-landing.php` (line 14)

Current CSS: `class="relative h-[600px] lg:h-[800px] overflow-hidden"`

The hero section uses:
- Fixed height: 600px on mobile, 800px on desktop
- Content vertically centered with `justify-center`
- All content inside: edition badge, title (large Bebas Neue), subtitle, 4 info rows with icons, and 2 CTA buttons

On 320px width viewports with 568px height, the total content height exceeds 600px, causing the second button to be cut off.

Verified working on:
- 360x800 (Android): Both buttons fully visible
- 375x812 (iPhone 12/13/14): Both buttons fully visible
- 768x1024 (iPad): Buttons side-by-side, both visible
- 1440x900 (Laptop): Perfect
- 1920x1080 (Desktop): Perfect

## Workflow Notes

### MCP Playwright Tool Limitations

The MCP Playwright browser tools have limited functionality:
- Available: navigate, resize, screenshot, snapshot, close
- **Not available: click, type, keyboard, mouse events**

For testing that requires interaction (clicking buttons, opening modals, filling forms):
1. Create standalone Playwright test script in `.qa/` directory
2. Use existing `.qa/package.json` with Playwright dependency
3. Provide bash script to run tests
4. Alternative: Verify behavior through code review + provide manual test plan

## Resolved Issues

### Metrics Section Label Alignment (front-page.php)

Previously reported issue: "Edición" label under "27ª" was being pushed down by the superscript "ª", causing misalignment with other labels.

Status: **FIXED** as of 2026-02-18.

Current code in `/home/amatasuarez/workspace/bars-web/themes/bars2026/php/front-page.php` (line 123):
- The "ª" superscript uses `relative -top-2.5 lg:-top-4` positioning (negative top = pulls up)
- Labels use `mt-1` margin which gives consistent spacing

Verified working at:
- 320x568: 2x2 grid, "Edición" and "Películas*" labels correctly vertically aligned
- 375x812: 1-row flex layout, all 4 labels correctly aligned
- 1024x768: 1-row flex layout, all 4 labels correctly aligned
- 1440x900: 1-row flex layout, all 4 labels perfectly aligned

### Metrics Grid Layout Breakpoints (front-page.php)

The metrics container uses: `grid grid-cols-2 min-[360px]:flex min-[360px]:flex-wrap min-[360px]:justify-center`

- Below 360px: `grid-cols-2` (2x2 grid)
- At 360px+: `flex flex-wrap justify-center` (all 4 in one row if space allows)
- At 375px: all 4 fit in a single row (no 3+1 orphan issue)

## Patterns

### Button Styles

The theme uses two button styles defined in `/home/amatasuarez/workspace/bars-web/themes/bars2026/css/main.css`:

- `.btn-primary`: Red background (#8B0000), white text, uppercase
- `.btn-ghost`: Transparent background, white border, white text, uppercase

Both buttons have responsive padding adjustments for screens < 374px (lines 122-130):
- Reduced horizontal padding: 0.5rem (from 2rem)
- Reduced font size: 0.75rem (from 0.875rem)
- Reduced letter spacing: 0.02em (from 0.05em)

### Hero Section Layout

Standard pattern for hero sections in this theme:
- Fixed height approach: `h-[600px] lg:h-[800px]`
- Dark background with overlay gradients
- Content vertically centered
- Content max-width: 700px
- Responsive typography scaling
