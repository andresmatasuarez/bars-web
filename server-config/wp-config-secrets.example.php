<?php
// Copy this file to wp-config-secrets.php on the production server
// and fill in the actual values. NEVER commit the real secrets file.

define('DB_NAME',     'your_db_name');
define('DB_USER',     'your_db_user');
define('DB_PASSWORD', 'your_db_password');
define('DB_HOST',     'localhost');
define('DB_CHARSET',  'utf8');
define('DB_COLLATE',  '');

// Generate at https://api.wordpress.org/secret-key/1.1/salt/
define('AUTH_KEY',         'put unique phrase here');
define('SECURE_AUTH_KEY',  'put unique phrase here');
define('LOGGED_IN_KEY',    'put unique phrase here');
define('NONCE_KEY',        'put unique phrase here');
define('AUTH_SALT',        'put unique phrase here');
define('SECURE_AUTH_SALT', 'put unique phrase here');
define('LOGGED_IN_SALT',   'put unique phrase here');
define('NONCE_SALT',       'put unique phrase here');
