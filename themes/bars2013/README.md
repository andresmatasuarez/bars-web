# bars2013

Legacy WordPress theme for Buenos Aires Rojo Sangre (2013-2025).

## Tech Stack

- **Styles**: LESS
- **JS**: jQuery, legacy JS bundled as IIFE (`bars.js`)
- **Selection app**: React 18, styled-components, Font Awesome (separate entry point → `selection.js`)
- **Build**: Vite
- **PHP deps**: Composer (PHPMailer in `vendor/`)

## Directory Structure

```
bars2013/
├─ assets/           # Source files (LESS, TypeScript, React apps, fonts)
├─ php/              # Theme-specific WordPress PHP templates
├─ vite/             # Vite configuration
├─ raw/              # Raw assets, .psd files or misc stuff
├─ vendor/           # PHP Composer dependencies
├─ package.json
└─ tsconfig.json
```

## Build

Two Vite entry points:

| Config                       | Output         | Format |
|------------------------------|----------------|--------|
| `vite/vite.config.ts`        | `bars.js`      | IIFE   |
| `vite/selection.vite.config.ts` | `selection.js` | IIFE   |

Build output goes to `wp-themes/bars2013/`.

## PHP Dependencies

```sh
cd themes/bars2013 && composer install
```

Required for PHPMailer (contact form).
