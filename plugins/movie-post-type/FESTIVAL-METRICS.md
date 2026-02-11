# Festival Metrics — How the Landing Page Stats Work

The BARS landing page displays four dynamic metrics in the "About" section. All values come from the **last completed edition** (not averages).

## Metrics Overview

| Metric | Function | Display | Description |
|---|---|---|---|
| **Ediciones** | `getFestivalMetrics()` | Exact number | Edition number (e.g., 26) |
| **Peliculas por ano** | `getMovieCount()` | Exact count | Feature-length films, not in a movieblock |
| **Cortos** | `getShortFilmCount()` | Estimated, with `+` | Short films (estimated via A+B formula) |
| **Paises** | `getCountryCount()` | Exact count, with `+` | Distinct countries across features + shorts |

## Which Edition? — `Editions::lastCompleted()`

Defined in `shared/php/editions.php`. Logic:

1. Get the current edition (highest `number` in `editions.json`).
2. If the edition's `days.from` date exists:
   - Compute a threshold = `from - 7 days`.
   - If **today < threshold** (i.e., the edition hasn't started and isn't about to), use the **previous** edition (`number - 1`).
3. Otherwise, use the current edition.

This means once we're within 7 days of the festival start, metrics switch to the new edition (even if data is incomplete). Outside that window, we show the previous edition's finalized data.

## Movie Count — `getMovieCount($editionKey)`

Counts published `movie` posts for the given edition, **excluding**:
- Short film sections: `shortFilm`, `shortFilmCompetition`, `cortos25anos`
- Online activities: `onlineActivities`
- Movies inside a movieblock (where `_movie_movieblock` is a valid post ID)

This gives the count of standalone feature-length/medium-length films that appear as individual entries in the programming.

### SQL Logic

```sql
SELECT COUNT(DISTINCT post.ID)
FROM posts post
JOIN postmeta m_edition  -- _movie_edition = $editionKey
JOIN postmeta m_section  -- _movie_section NOT IN (excluded)
LEFT JOIN postmeta m_mb  -- _movie_movieblock
WHERE post_status = 'publish' AND post_type = 'movie'
  AND (m_mb.meta_value IS NULL OR '' OR '-1')  -- not in a movieblock
```

## Short Film Count — `getShortFilmCount($editionKey)`

Short films are complex because they can be entered in the DB in two different ways:

1. **Individually inside a movieblock** — each short is a separate `movie` post linked to a `movieblock` via `_movie_movieblock`. These are fully enumerable. By convention, **any movie assigned to a movieblock is a short film**, regardless of its `_movie_section` value.
2. **As a standalone entry** — a single `movie` post in a short film section, not linked to a movieblock. We don't know how many individual shorts are inside.

### The A + B x avg Formula

- **A** = count of individual short films that belong to a movieblock
- **block_count** = number of distinct movieblocks that contain those shorts
- **avg** = A / block_count if block_count > 0, otherwise **1** (fallback: each standalone entry = 1 short)
- **B** = count of standalone short-section entries NOT in a movieblock
- **Result** = `ceil(A + B x avg)`

### Example

Suppose for bars26:
- 3 movieblocks have their shorts fully entered: 8, 6, and 10 shorts = **A = 24**, block_count = 3, avg = 8
- 2 standalone short entries exist (not in a movieblock) = **B = 2**
- Estimated total = ceil(24 + 2 x 8) = ceil(40) = **40 shorts**

### Short Film Sections

The following `_movie_section` values are considered short film sections:
- `shortFilm` — Cortos fuera de competencia
- `shortFilmCompetition` — Cortos en competencia
- `cortos25anos` — Cortos BARS 25 anos (special edition section)

Note: `onlineActivities` is excluded from everything (not a film section).

### SQL Logic (two queries)

**Query A** — shorts in movieblocks (any movie assigned to a valid movieblock, regardless of section):
```sql
SELECT COUNT(DISTINCT post.ID) AS total,
       COUNT(DISTINCT m_mb.meta_value) AS block_count
FROM posts post
JOIN postmeta m_edition  -- _movie_edition = $editionKey
JOIN postmeta m_mb       -- _movie_movieblock (valid ID)
WHERE post_status = 'publish' AND post_type = 'movie'
  AND m_mb.meta_value IS NOT NULL AND != '' AND != '-1'
```

**Query B** — standalone entries:
```sql
SELECT COUNT(DISTINCT post.ID)
FROM posts post
JOIN postmeta m_edition  -- _movie_edition = $editionKey
JOIN postmeta m_section  -- _movie_section IN (short sections)
LEFT JOIN postmeta m_mb  -- _movie_movieblock
WHERE post_status = 'publish' AND post_type = 'movie'
  AND (m_mb.meta_value IS NULL OR '' OR '-1')
```

## Country Count — `getCountryCount($editionKey)`

Counts distinct countries across **both** feature films and short films that are inside movieblocks. The OR condition includes a movie if:

- Its section is NOT in the excluded list (i.e., it's a feature/medium-length film), **OR**
- It belongs to a valid movieblock (i.e., it's an individually-entered short film with country data)

Multi-country values (e.g., "Argentina/Chile", "USA, UK") are split by `/`, `,`, and `-` separators via `parseCountryField()`, normalized to lowercase, and deduplicated.

### SQL Logic

```sql
SELECT DISTINCT m_country.meta_value AS country
FROM posts post
JOIN postmeta m_edition  -- _movie_edition = $editionKey
JOIN postmeta m_section  -- _movie_section
JOIN postmeta m_country  -- _movie_country (non-empty)
LEFT JOIN postmeta m_mb  -- _movie_movieblock
WHERE post_status = 'publish' AND post_type = 'movie'
  AND (
    m_section.meta_value NOT IN (excluded)   -- feature films
    OR (m_mb valid)                          -- shorts in blocks
  )
```

Then each raw country value is parsed in PHP and deduplicated.

## Caching

- **Transient key**: `bars_festival_metrics`
- **TTL**: 7 days
- **Invalidation**: Automatically deleted whenever a `movie` or `movieblock` post is saved (via `save_post_movie` and `save_post_movieblock` action hooks calling `invalidateFestivalMetricsCache()`).

## File Locations

| File | What it does |
|---|---|
| `shared/php/editions.php` | `Editions::lastCompleted()` — determines which edition to use |
| `plugins/movie-post-type/movie-post-type.php` | All metric functions (`getFestivalMetrics`, `getMovieCount`, `getCountryCount`, `getShortFilmCount`, `parseCountryField`, cache invalidation) |
| `themes/bars2026/php/front-page.php` | Renders the 4 stat boxes in the "About" section |
