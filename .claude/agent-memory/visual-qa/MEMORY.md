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
