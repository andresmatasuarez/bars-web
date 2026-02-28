# Integration Tests

HTTP-level tests that run against the Docker WordPress instance. They verify edition-aware redirect logic, OG tags, canonical URLs, meta descriptions, JSON-LD structured data, and other SEO behaviour using `fetch()` and WP-CLI — no additional dependencies required (Node 22+).

Edition numbers are read dynamically from `shared/editions.json`, so tests adapt automatically when a new edition is added. If the current edition lacks seed data for movies, movieblocks, or jury members, the test script creates temporary posts and cleans them up on exit.

## Prerequisites

- Docker containers running with seed data: `docker compose up -d`
- bars2026 theme built: `npm run build:bars2026`
- Seed data must include movies and jury members from at least two editions

## Running

```bash
npm run test:integration
```

## Test groups

| Group | What it tests                                            | PHP file             |
| ----- | -------------------------------------------------------- | -------------------- |
| **A** | Selection page redirects (`?f=` param)                   | `page-selection.php` |
| **B** | Awards page redirects (`?j=` param)                      | `page-awards.php`    |
| **C** | Native WP URL redirects (301 → modal pages)              | `seo.php`            |
| **D** | OG tag correctness (og:url, og:title, og:image, noindex) | `seo.php`            |
| **E** | Movieblock native URL redirects (301)                    | `seo.php`            |
| **F** | Canonical URLs                                           | `seo.php`            |
| **G** | Meta descriptions                                        | `seo.php`            |
| **H** | JSON-LD structured data                                  | `seo.php`            |
| **I** | Miscellaneous SEO (noindex, robots.txt, og:type)         | `seo.php`            |
