<?php

// tests/bootstrap.php (ejemplo)
$_SERVER['REQUEST_URI']    = $_SERVER['REQUEST_URI']    ?? '/';
$_SERVER['QUERY_STRING']   = $_SERVER['QUERY_STRING']   ?? '';
$_SERVER['HTTP_HOST']      = $_SERVER['HTTP_HOST']      ?? 'localhost';

// Luego cargas el resto
require_once __DIR__ . '/../polaris.php';

?>