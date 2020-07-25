<?php

use Thenoun\Utils\Router;

/**
 * Main entry point for the application
 * Serves as request dispatcher
 */

define( 'ROOT', __DIR__ );

// Autoloading and global settings
require ROOT . '/vendor/autoload.php';
require ROOT . '/src/Config/settings.php';

// Handle dispatching
$router = ( new Router( ROUTES ) )->dispatch();
