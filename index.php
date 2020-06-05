<?php
/**
 * Main entry point for the application
 * Serves as request dispatcher
 */

define('ROOT', __DIR__);

// Autoloading and global settings
$loader = require ROOT . '/vendor/autoload.php';
require ROOT . '/src/config/settings.php';

// Handle dispatching
$router = (new Router(ROUTES))->dispatch();
