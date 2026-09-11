<?php

/**
 * Absensi Banat Router Script for PHP Built-in Server
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// If the requested URI is an existing file (not a directory), serve it directly
if ($uri !== '/' && file_exists(__DIR__ . '/public_html' . $uri) && !is_dir(__DIR__ . '/public_html' . $uri)) {
    return false;
}

// Forward all other requests (including Laravel routes like /admin/dashboard) to index.php
require_once __DIR__ . '/public_html/index.php';
