# bars2026

Current WordPress theme for Buenos Aires Rojo Sangre (2026+).

## Tech Stack

- **Styles**: Tailwind CSS v4
- **JS/TS**: TypeScript, React 19 (integrated components, not a separate SPA)
- **Build**: Vite
- **No PHP Composer** dependencies

## Directory Structure

```
bars2026/
├─ css/              # Tailwind CSS source
├─ ts/               # TypeScript + React components
├─ php/              # Theme-specific WordPress PHP templates
├─ vite/             # Vite configuration
├─ pencil-design/    # .pen design source files
├─ package.json
└─ tsconfig.json
```

## Build

Single Vite entry point:

| Config                | Output                        | Format |
|-----------------------|-------------------------------|--------|
| `vite/vite.config.ts` | `bars2026.js` + `bars2026.css` | IIFE   |

Build output goes to `wp-themes/bars2026/`.

## Design Files

`.pen` files in `pencil-design/` are the source designs for this theme. They can be read and edited using the Pencil MCP tools.
