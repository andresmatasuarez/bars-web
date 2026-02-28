# Integration Tests

HTTP-level tests that run against the Docker WordPress instance. They verify edition-aware redirect logic and OG tag correctness using `fetch()` and WP-CLI — no additional dependencies required (Node 22+).

## Prerequisites

- Docker containers running with seed data: `docker compose up -d`
- bars2026 theme built: `npm run build:bars2026`
- Seed data must include movies and jury members from at least two editions (bars25 and bars26)

## Running

```bash
npm run test:integration
```

## Test groups

| Group | What it tests | PHP file |
|-------|--------------|----------|
| **A** | Selection page redirects (`?f=` param) | `page-selection.php` |
| **B** | Awards page redirects (`?j=` param) | `page-awards.php` |
| **C** | Native WP URL redirects (301 → modal pages) | `seo.php` |
| **D** | OG tag correctness (og:url, og:title, og:image, noindex) | `seo.php` |
