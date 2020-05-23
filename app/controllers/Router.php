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
    public function __construct($routes, $request)
    {
        $this->routes = $routes;
        $this->request = $request;
    }
    /**
     * Rerouting requests
     */
    public function dispatch()
    {

        foreach ($this->routes as $route) {
            if ($route['endpoint'] === $this->request) {

                // If class exists, use it
                if (class_exists($route['controller'])) {
                    $controller = new $route['controller']();

                    // Check and select the method to call
                    if (method_exists($controller, $route['method'])) {
                        $method = $route['method'];
                        // passing the request into the method
                        $controller->$method($this->request);
                        return;
                    }

                }
            }

        }

        NotFoundController::print();
    }
}
