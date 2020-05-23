<?php
/**
 * Main entry point for the application
 * Serves as request dispatcher
 */

define('ROOT', __DIR__);

require ROOT . "/settings.php";
require ROOT . "/app/controllers/Router.php";
require ROOT . "/app/controllers/Controller.php";
require ROOT . "/app/controllers/HomeController.php";
require ROOT . "/app/controllers/AuthController.php";
require ROOT . "/app/controllers/NotFoundController.php";
require ROOT . "/app/utils/Mediawiki.php";

// Define routes
$routes = array(
    /**
     * Home endpoints
     */
    array(
        'endpoint' => '/',
        'controller' => 'HomeController',
        'method' => 'index',
    ),
    array(
        'endpoint' => '/test',
        'controller' => 'HomeController',
        'method' => 'test',
    ),
    /**
     * Authentication endpoints
     */
    array(
        'endpoint' => '/login',
        'controller' => 'AuthController',
        'method' => 'login',
    ),
    array(
        'endpoint' => '/oauth-callback',
        'controller' => 'AuthController',
        'method' => 'callback',
    ),
    array(
        'endpoint' => '/logout',
        'controller' => 'AuthController',
        'method' => 'logout',
    ),
);

// Handle dispatching
$request = $_SERVER['REQUEST_URI'];
$router = (new Router($routes, $request))->dispatch();
