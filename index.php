<?php
/**
 * Main entry point for the application
 * Serves as request dispatcher
 */


define('ROOT', __DIR__);

require ROOT . "/settings.php";
require ROOT . "/app/controllers/Router.php";
require ROOT . "/app/controllers/Request.php";
require ROOT . "/app/controllers/Controller.php";
require ROOT . "/app/controllers/HomeController.php";

// Handle routing
$routes = array(
    array('/', array('HomeController', 'index')),
    array('/login', array('HomeController', 'login')),
);

$request = $_SERVER['REQUEST_URI'];
$router = new Router($routes, $request);
$router->dispatch();