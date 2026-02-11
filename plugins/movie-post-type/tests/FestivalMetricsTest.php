<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/MockWpdb.php';

class FestivalMetricsTest extends TestCase
{
    /** @var MockWpdb */
    private $wpdb;

    protected function setUp(): void
    {
        $this->wpdb = new MockWpdb();
        $GLOBALS['wpdb'] = $this->wpdb;
        $GLOBALS['_transients'] = [];
    }

    // ── getMovieCount ────────────────────────────────────────────────────────

    public function testGetMovieCountReturnsInt()
    {
        $this->wpdb->pushGetVar(42);
        $this->assertSame(42, getMovieCount('bars26'));
    }

    // ── getCountryCount ──────────────────────────────────────────────────────

    public function testGetCountryCountMultiCountryDedup()
    {
        // Two rows: "Argentina/Chile" and "Chile, Brasil"
        $this->wpdb->pushGetResults([
            (object) ['country' => 'Argentina/Chile'],
            (object) ['country' => 'Chile, Brasil'],
        ]);

        // argentina, chile, brasil = 3 distinct → floor(3/5)*5 = 0
        $this->assertSame(0, getCountryCount('bars26'));
    }

    public function testGetCountryCountEmptyResults()
    {
        $this->wpdb->pushGetResults([]);
        $this->assertSame(0, getCountryCount('bars26'));
    }

    // ── getShortFilmCount ────────────────────────────────────────────────────

    public function testShortFilmCountNormal()
    {
        // A=24, blocks=3 → avg=8. B=2. ceil(24 + 2×8) = 40 → floor(40/10)*10 = 40
        $this->wpdb->pushGetRow((object) ['total' => 24, 'block_count' => 3]);
        $this->wpdb->pushGetVar(2);

        $this->assertSame(40, getShortFilmCount('bars26'));
    }

    public function testShortFilmCountNoBlocks()
    {
        // A=0, blocks=0 → avg fallback=1. B=15. ceil(0 + 15×1) = 15 → floor(15/10)*10 = 10
        $this->wpdb->pushGetRow((object) ['total' => 0, 'block_count' => 0]);
        $this->wpdb->pushGetVar(15);

        $this->assertSame(10, getShortFilmCount('bars26'));
    }

    public function testShortFilmCountAllEntered()
    {
        // A=26, blocks=4. B=0. ceil(26 + 0×6.5) = 26 → floor(26/10)*10 = 20
        $this->wpdb->pushGetRow((object) ['total' => 26, 'block_count' => 4]);
        $this->wpdb->pushGetVar(0);

        $this->assertSame(20, getShortFilmCount('bars26'));
    }

    public function testShortFilmCountNothing()
    {
        // A=0, blocks=0. B=0. ceil(0) = 0
        $this->wpdb->pushGetRow((object) ['total' => 0, 'block_count' => 0]);
        $this->wpdb->pushGetVar(0);

        $this->assertSame(0, getShortFilmCount('bars26'));
    }

    public function testShortFilmCountFractional()
    {
        // A=7, blocks=2 → avg=3.5. B=3. ceil(7 + 3×3.5) = ceil(17.5) = 18 → floor(18/10)*10 = 10
        $this->wpdb->pushGetRow((object) ['total' => 7, 'block_count' => 2]);
        $this->wpdb->pushGetVar(3);

        $this->assertSame(10, getShortFilmCount('bars26'));
    }

    // ── getFestivalMetrics caching ───────────────────────────────────────────

    public function testGetFestivalMetricsCacheHit()
    {
        $cached = ['editions' => 25, 'movies' => 100, 'short_films' => 50, 'countries' => 30];
        $GLOBALS['_transients']['bars_festival_metrics'] = $cached;

        $this->assertSame($cached, getFestivalMetrics());
    }

    public function testGetFestivalMetricsCacheMiss()
    {
        // getMovieCount → get_var
        $this->wpdb->pushGetVar(80);
        // getShortFilmCount → get_row + get_var
        $this->wpdb->pushGetRow((object) ['total' => 10, 'block_count' => 2]);
        $this->wpdb->pushGetVar(5);
        // getCountryCount → get_results
        $this->wpdb->pushGetResults([
            (object) ['country' => 'Argentina'],
            (object) ['country' => 'Brasil'],
        ]);

        $metrics = getFestivalMetrics();

        $this->assertArrayHasKey('editions', $metrics);
        $this->assertSame(80, $metrics['movies']);
        // A=10, blocks=2, avg=5, B=5 → ceil(10 + 5×5) = 35 → floor(35/10)*10 = 30
        $this->assertSame(30, $metrics['short_films']);
        // 2 countries → floor(2/5)*5 = 0
        $this->assertSame(0, $metrics['countries']);

        // Should now be cached
        $this->assertSame($metrics, get_transient('bars_festival_metrics'));
    }
}
