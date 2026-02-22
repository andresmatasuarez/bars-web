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

## SEO

Custom PHP SEO implementation (no plugin). Hooks into `wp_head` to output:

- **Meta descriptions** — auto-generated per page type (post excerpt, movie synopsis, per-page defaults)
- **Open Graph tags** — `og:title`, `og:description`, `og:image`, `og:url`, `og:type`, `og:locale`
- **Twitter Card tags** — `summary_large_image` cards mirroring OG data
- **Canonical URLs** — `<link rel="canonical">` on all pages
- **JSON-LD structured data**:
  - `Organization` (all pages) — festival name, social profiles
  - `WebSite` + `Event` (front page) — festival dates, venues
  - `NewsArticle` (single posts) — headline, dates, image
  - `Movie` (single movies) — title, directors, country, duration, synopsis
- **robots.txt** — adds `Sitemap:` directive pointing to WordPress's native XML sitemap

All SEO logic lives in `php/seo.php`.

## Modal URL Parameters

Modal content (movies, jury members) is linked via short query params:

| Page | Param | Example |
|---|---|---|
| `/programacion` | `?f=<slug>` | `/programacion?f=predio-vazio` |
| `/premios` | `?j=<slug>` | `/premios?j=john-doe` |

**Why not `?movie=` / `?jury=`?** WordPress registers CPT names as query vars when `public => true` in `register_post_type`. Using `?movie=<slug>` or `?jury=<slug>` on a page URL causes WordPress to interpret it as a CPT query, hijacking the main query and returning a 404 when the slug doesn't match the expected page. Additionally, `?m=` is a WordPress core query var used for month-based archive queries, which causes similar 404 issues. Short params (`f`, `j`) avoid these conflicts.

Standalone CPT URLs (`/movie/<slug>`, `/jury/<slug>`) are 301-redirected to the corresponding modal URL via `template_redirect` hooks in `php/seo.php`.

## Design Files

`.pen` files in `pencil-design/` are the source designs for this theme. They can be read and edited using the Pencil MCP tools.
