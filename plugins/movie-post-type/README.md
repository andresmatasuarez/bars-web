# Movie Post Type

WordPress plugin that registers the **Movie** and **Movieblock** custom post types for the Buenos Aires Rojo Sangre film festival. Also provides the festival metrics functions used on the landing page (movie count, short film estimate, country count).

## Testing

Tests cover the pure-PHP logic (country parsing, metrics formulas, edition helpers) using lightweight WordPress stubs — no running WordPress instance is needed.

### Prerequisites

- **Docker** (the WordPress container image provides PHP 8.2 with all required extensions)

### One-time setup

Install Composer dependencies. If your host PHP lacks `ext-dom`, add `--ignore-platform-reqs`:

```bash
cd plugins/movie-post-type
composer install --ignore-platform-reqs
```

### Running the tests

From the project root:

```bash
docker run --rm \
  -v "$PWD":/app \
  -w /app/plugins/movie-post-type \
  -e BARS_PROJECT_ROOT=/app \
  bitnamilegacy/wordpress:latest \
  php vendor/bin/phpunit
```

This mounts the entire project at `/app` so the bootstrap can find `shared/editions.json` and `shared/php/`. The `BARS_PROJECT_ROOT` env var tells the bootstrap where to look.

### `BARS_PROJECT_ROOT`

The test bootstrap resolves `shared/` relative to the project root. By default it assumes three directories up from `tests/` (i.e., `plugins/movie-post-type/tests/../../..`). Set `BARS_PROJECT_ROOT` to override this when the directory layout differs (e.g., inside Docker).

## Further reading

See [FESTIVAL-METRICS.md](FESTIVAL-METRICS.md) for detailed documentation on how the landing page metric functions work.
