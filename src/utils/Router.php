<?php
/**
 * Router responsible for redirecting
 * incoming request and mapping them
 * to the correct controller
 */
class Router
{

    private $routes;
    private $request;

    /**
     * Constructor
     */
    public function __construct($routes)
    {
        $this->routes = $routes;
        $this->request = $_SERVER['REQUEST_URI'];
    }
    /**
     * Rerouting requests
     */
    public function dispatch()
    {

        foreach ($this->routes as $route) {
            $endpoint = explode("?", $this->request)[0];
            if ($route['endpoint'] === $endpoint) {

                // If class exists, use it
                if (class_exists($route['controller'])) {
                    $controller = new $route['controller']();
                    $controller->middleWare($route);
                    // Check and select the method to call
                    if (method_exists($controller, $route['method'])) {
                        $method = $route['method'];
                        // passing the request into the method
                        $controller->$method($this->request);
                        exit();
                    }
                }
            }

        }

        NotFoundController::print();
    }

    /**
     * Setup the session cookie
     * @return void
     */
    public static function getCookie($key)
    {
        session_start();
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        session_write_close();
    }

    /**
     * Setup the session cookie
     * @return void
     */
    public static function getSession()
    {
        session_start();
        return $_SESSION;
        session_write_close();
    }

    /**
     * Setup the session cookie
     * @return void
     */
    public static function setCookie($key, $value)
    {
        session_start();
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key] = $value;
        }
        session_write_close();
    }

}
