# Visual QA Memory

## Known Issues

### Hero Section Button Layout (320x568 - iPhone SE)

Issue: On very small mobile viewports (320x568), the hero section has a fixed height of 600px, causing the "Convocatoria abierta" button to be cut off below the viewport fold.

Location: `/home/amatasuarez/workspace/bars-web/themes/bars2026/php/template-parts/sections/hero-landing.php` (line 14)

Current CSS: `class="relative min-h-[600px] lg:h-[800px] overflow-hidden"` (updated to `min-h` on mobile)

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

## Previous Edition Showcase Section

File: `/home/amatasuarez/workspace/bars-web/themes/bars2026/php/template-parts/sections/previous-edition-showcase.php`

### When it renders
- Renders when current edition is missing poster AND/OR spot
- Normal `spot-section.php` correctly returns early when poster OR spot is missing

### Cases verified

#### Case 1 (both null - historical): poster=null, spot=null
- Shows previous edition poster + spot side-by-side on desktop
- Heading: "Edición Anterior", Caption: "Afiche y spot de la edición anterior"
- Badge: "EDICIÓN XXVI · 2025"

#### Case 2 (poster exists, spot null): poster set, spot=null
- State as of 2026-02-19: edition 27 has poster="resources/bars2025/bars2025_afiche.jpg", spot=null
- Hero shows current poster on desktop (left of title), below CTAs on mobile
- Showcase renders spot-only centered layout at max-w-[900px]
- Heading: "Spot edición anterior" (lowercase 'e'), Caption: "Spot correspondiente a la 26ª edición (BARS XXVI · 2025)"
- Badge: "EDICIÓN XXVI · 2025"
- Caption uses `opacity-50 text-bars-text-muted` - visually faded/muted
- Second line: "Afiche y spot 2026 próximamente disponibles" also with `opacity-50 text-bars-text-muted`
- Normal spot-section.php does NOT render (correctly - returns early on !$spot_url)
- Showcase appears AFTER About section (section order: Hero → Spot → News → About → Showcase)
- Verified PASS at: Desktop 1280x900, Mobile 390x844 (2026-02-19)

#### Case 4 (both set): poster set, spot set
- State as of 2026-02-18: edition 27 has poster="resources/bars2025/bars2025_afiche.jpg", spot="https://www.youtube.com/watch?v=gPBq2auZmeI"
- previous-edition-showcase.php line 13 guard (`if ($poster && $spot_url) return`) triggers — showcase does NOT render
- spot-section.php renders normally since both poster and spot are present
- Hero shows poster (left of title on desktop)
- Spot section heading: "Spot Oficial", Badge: "EDICIÓN XXVII", Caption: "Spot oficial BARS XXVII"
- No "Edición Anterior" heading anywhere on the page
- Verified PASS at: Desktop 1280x900 (2026-02-18)

### Layout behavior (has_both = poster + spot)
- Desktop (1024px+, `lg:` breakpoint): Side-by-side horizontal layout, poster 280px left + spot flex-1 right
- Mobile/Tablet (<1024px): Stacks vertically, poster centered (220px fixed width) then spot full-width below

### Layout behavior (spot only, no poster in showcase)
- Container max-w-[900px] mx-auto
- Video uses full container width with aspect-video ratio
- Centered at all viewports

### Width mismatch fix applied (2026-02-18)
Spot container uses: `w-full max-w-lg lg:max-w-none lg:flex-1 lg:min-w-0`

Result at each viewport (Case 1):
- Mobile 375px: Spot = 335px (content area, max-w-lg doesn't kick in), Poster = 220px. Still narrower
- Tablet 768px: Spot = 512px (max-w-lg), Poster = 220px. Improved from 708px. Both centered
- Desktop 1280px: Side-by-side, both lg: layout, no issue

The max-w-lg fix partially resolves the imbalance at tablet. A minor width difference between poster (~220px) and spot (~510px at tablet) remains, but is acceptable. The spot is no longer edge-to-edge at tablet.

### Content verified correct (Case 2)
- Badge: "EDICIÓN XXVI · 2025" - renders with correct text and red styling
- Heading: "Spot Edición Anterior" - renders in serif heading font
- Caption: "Spot de la edición anterior" - centered below
- Background: `bg-bars-bg-medium` - correct dark medium background

### Screenshot artifacts
Element-level screenshots (via `page.getByText().screenshot()`) often crop at the viewport boundary, making the spot appear clipped on the right. Always take viewport-level screenshots to confirm actual rendering.

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
