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
require ROOT . "/app/controllers/NotFoundController.php";

// Define routes
$routes = array(
    array(
        'endpoint' => '/',
        'controller' => 'HomeController',
        'method' => 'index',
    ),
    array(
        'endpoint' => '/login',
        'controller' => 'HomeController',
        'method' => 'login',
    ),
);

// Handle dispatching
$request = $_SERVER['REQUEST_URI'];
$router = new Router($routes, $request);
$router->dispatch();
