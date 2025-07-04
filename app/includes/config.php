<?php
error_reporting(0);
define('FIR', true);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_NAME', 'phpsearchengine_db');
define('DB_PASS', '');

// define('URL_PATH', 'https://example.com');
define('URL_PATH', 'http://localhost/phpsearchengine.dev');

define('PUBLIC_PATH', 'public');
define('THEME_PATH', 'themes');
define('STORAGE_PATH', 'storage');
define('UPLOADS_PATH', 'uploads');

define('COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', URL_PATH).'/');