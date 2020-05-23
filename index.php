<?php
/**
 * Main entry point for the application
 * Serves as request dispatcher
 */

define('ROOT', __DIR__);

require ROOT . "/settings.php";
require ROOT . "/app/controllers/Controller.php";
require ROOT . "/app/controllers/HomeController.php";
require ROOT . "/app/controllers/AuthController.php";
require ROOT . "/app/controllers/NotFoundController.php";
require ROOT . "/app/utils/Logger.php";
require ROOT . "/app/utils/MediaWiki.php";
require ROOT . "/app/utils/Router.php";

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
