<?php
/**
 * GK224.COM - Global Configuration & Environment Settings
 */

// Start session securely if not already started and headers not yet sent
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @ini_set('session.cookie_httponly', 1);
    @ini_set('session.use_only_cookies', 1);
    @session_start();
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// App Settings
define('APP_NAME', 'GK224.COM');
define('APP_TAGLINE', 'Pay · Learn · Earn · Travel');
define('APP_ENV', 'production'); // 'development' or 'production'

// Base URL calculation (works on localhost, subfolders and live domain automatically)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($protocol . $host . ($scriptDir === '/' ? '' : $scriptDir), '/');
define('BASE_URL', $baseUrl);

// Database Credentials (Optional / For MySQL integration)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gk224_db');

// Error Reporting based on environment
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
