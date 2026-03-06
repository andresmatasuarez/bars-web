<?php
// Load secrets (DB credentials + salts) from a file outside version control.
// See docs/server-access.md for first-time setup instructions.
require __DIR__ . '/wp-config-secrets.php';

// Site URLs (override DB values)
define('WP_HOME', 'https://' . $_SERVER['SERVER_NAME']);
define('WP_SITEURL', 'https://' . $_SERVER['SERVER_NAME']);

$table_prefix = 'wp_';

define('WPLANG', 'es_ES');
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');
