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
            if ($route == $this->request) {
                // If class exists, use it
                if (class_exists($classname)) {
                    $controller = new $classname();

                    // Check and select the method to call
                    $method = strtolower($this->request->method);
                    if (method_exists($controller, $method)) {

                        // passing the request into the method
                        $controller->$method($this->request);
                        return;
                    }

                }
                return true;
            }

        }
        /*
        if(isset($this->request->controller)) {
        $;
        $classname = $endpoint . "Controller";

        // If class exists, use it
        if ( class_exists( $classname ) ) {
        $controller = new $classname();

        // Check and select the method to call
        $method = strtolower($this->request->method);
        if(method_exists($controller, $method)) {

        // passing the request into the method
        $controller->$method($this->request);
        return;
        }

        }

        }*/

        //NotFoundController::print();
    }
}
