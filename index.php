<?php

use Thenoun\Config\Settings;
use Thenoun\Utils\Router;

/**
 * Main entry point for the application
 * Serves as request dispatcher
 */

define( 'ROOT', __DIR__ );

// Autoloading and global settings
require ROOT . '/vendor/autoload.php';

// Handle dispatching
$router = ( new Router( Settings::$ROUTES ) )->dispatch();
