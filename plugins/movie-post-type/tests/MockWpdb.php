<?php
/**
 * Queue-based $wpdb mock for testing metric functions.
 *
 * Enqueue expected return values with pushGetVar / pushGetRow / pushGetResults,
 * then let the tested function call get_var / get_row / get_results in order.
 */
class MockWpdb {
    public $prefix = 'wp_';

    private $getVarQueue    = [];
    private $getRowQueue    = [];
    private $getResultsQueue = [];

    // ── Queue helpers ────────────────────────────────────────────────────────

    public function pushGetVar($value) {
        $this->getVarQueue[] = $value;
    }

    public function pushGetRow($value) {
        $this->getRowQueue[] = $value;
    }

    public function pushGetResults($value) {
        $this->getResultsQueue[] = $value;
    }

    // ── wpdb interface ───────────────────────────────────────────────────────

    public function prepare($sql, ...$args) {
        // Flatten nested arrays (wpdb->prepare accepts both positional and array args)
        $flat = [];
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $v) { $flat[] = $v; }
            } else {
                $flat[] = $arg;
            }
        }
        // Return the raw SQL — we don't need real interpolation for tests
        return $sql;
    }

    public function get_var($query = null) {
        return array_shift($this->getVarQueue);
    }

    public function get_row($query = null) {
        return array_shift($this->getRowQueue);
    }

    public function get_results($query = null) {
        return array_shift($this->getResultsQueue);
    }

    // ── Assertions ───────────────────────────────────────────────────────────

    public function assertQueuesEmpty() {
        if (!empty($this->getVarQueue) || !empty($this->getRowQueue) || !empty($this->getResultsQueue)) {
            throw new \RuntimeException('MockWpdb queues not fully consumed');
        }
    }
}
