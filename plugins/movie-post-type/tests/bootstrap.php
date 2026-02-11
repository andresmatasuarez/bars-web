<?php
/**
 * PHPUnit bootstrap for movie-post-type tests.
 *
 * Stubs all WordPress functions/classes referenced during plugin load so we can
 * test pure-PHP logic (metrics, country parsing, Editions) without a running WP.
 */

// ── WordPress function stubs ─────────────────────────────────────────────────

function add_action() {}
function register_post_type() {}
function add_meta_box() {}
function wp_enqueue_script() {}
function plugins_url() { return ''; }
function wp_reset_query() {}
function get_post_meta() { return ''; }
function get_post_type() { return 'movie'; }
function get_the_title() { return ''; }
function get_post_permalink() { return ''; }
function get_the_post_thumbnail() { return ''; }

// Transient API backed by a simple array
$GLOBALS['_transients'] = [];

function get_transient($key) {
    return isset($GLOBALS['_transients'][$key]) ? $GLOBALS['_transients'][$key] : false;
}

function set_transient($key, $value, $ttl = 0) {
    $GLOBALS['_transients'][$key] = $value;
}

function delete_transient($key) {
    unset($GLOBALS['_transients'][$key]);
}

if (!defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}

// ── Minimal WP_Query stub ────────────────────────────────────────────────────

class WP_Query {
    private $posts = [];
    public function __construct($args = []) { $this->posts = []; }
    public function have_posts() { return false; }
    public function the_post() {}
}

// ── Jury stub (referenced by Editions::getJuries) ───────────────────────────

function getJuries($edition) { return []; }

// ── bars-commons edition options (used during plugin initialize()) ───────────

$GLOBALS['barscommons_editionOptions'] = [
    ['label' => 'BARS 26', 'value' => 'bars26'],
];

// Use $GLOBALS so the plugin's `global $barscommons_editionOptions` picks it up
$barscommons_editionOptions = &$GLOBALS['barscommons_editionOptions'];

// ── bars-commons save/show helpers ───────────────────────────────────────────

function barscommons_show_meta_box_inputs() {}
function barscommons_save_custom_post() {}

// ── Load shared PHP (editions + helpers) then the plugin ─────────────────────

// Allow overriding the project root (useful when running inside Docker where
// the shared/ directory may not be at the same relative path).
$projectRoot = getenv('BARS_PROJECT_ROOT') ?: realpath(__DIR__ . '/../../..');

// Suppress the warning from Editions::initialize() auto-loading from the wrong
// path (source tree vs built output); we re-initialize below with the right path.
@require_once $projectRoot . '/shared/php/helpers.php';

// In the source tree, editions.json lives at shared/editions.json but editions.php
// auto-initializes from dirname(__FILE__)/editions.json (which only works in the
// built output where both files are copied to the same directory). Re-initialize
// with the correct source-tree path.
Editions::initialize($projectRoot . '/shared/editions.json');

require_once __DIR__ . '/../movie-post-type.php';
