<?php
/**
 * Main entry point for the application
 * Serves as request dispatcher
 */

define('ROOT', __DIR__);

$loader = require ROOT . '/vendor/autoload.php';
require ROOT . '/src/config/settings.php';


// Define routes
$routes = array(
    /**
     * Home endpoints
     */
    array(
        'endpoint' => '/',
        'controller' => 'HomeController',
        'method' => 'index',
        'protected' => false
    ),
    array(
        'endpoint' => '/test',
        'controller' => 'HomeController',
        'method' => 'test',
        'protected' => false
    ),
    array(
        'endpoint' => '/upload',
        'controller' => 'HomeController',
        'method' => 'upload',
        'protected' => true
    ),
    /**
     * Authentication endpoints
     */
    array(
        'endpoint' => '/login',
        'controller' => 'AuthController',
        'method' => 'login',
        'protected' => false
    ),
    array(
        'endpoint' => '/oauth-callback',
        'controller' => 'AuthController',
        'method' => 'callback',
        'protected' => false
    ),
    array(
        'endpoint' => '/logout',
        'controller' => 'AuthController',
        'method' => 'logout',
        'protected' => false
    ),
);

// Handle dispatching
$request = $_SERVER['REQUEST_URI'];
$router = (new Router($routes, $request))->dispatch();
