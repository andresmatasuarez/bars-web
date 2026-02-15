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

## Screening Format

Movie screenings are stored in the `_movie_screenings` post meta field as a comma-separated string. Each screening follows one of three raw formats:

### Raw format patterns

| Type | Pattern | Example |
|---|---|---|
| Traditional (in-person) | `venue.room:mm-dd-yyyy hh:mm` | `belgrano.Sala 6:12-06-2021 20:00` |
| Streaming (always available) | `streaming!venue:full` | `streaming!vivamoscultura:full` |
| Streaming (day-specific) | `streaming!venue:mm-dd-yyyy` | `streaming!vivamoscultura:11-15-2023` |

### Parsing rules

- **`,`** (comma) separates multiple screenings within a single meta field
- **`.`** (dot) separates venue from room in traditional screenings (`belgrano.Sala 6`)
- **`:`** (colon) separates venue/room from date/time
- **`!`** (exclamation) marks the streaming prefix (`streaming!venue`)
- **`full`** keyword after the colon triggers always-available streaming (available for the entire festival duration)

### Date format

Dates use `m-d-Y` (month-day-year, e.g. `12-06-2021`). Times use 24-hour `H:i` format (e.g. `20:00`). Timezone is `America/Argentina/Buenos_Aires`.

### Data flow

1. Raw string stored in `_movie_screenings` WordPress post meta
2. Parsed by `shared/php/helpers.php:parseScreening()` into a PHP associative array
3. Serialized as JSON in `window.MOVIES` inside the `selection.php` template
4. Consumed by the React selection app using TypeScript types from `shared/ts/selection/types.ts`

## Further reading

See [FESTIVAL-METRICS.md](FESTIVAL-METRICS.md) for detailed documentation on how the landing page metric functions work.
