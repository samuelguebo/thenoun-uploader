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
    private function setupSession()
    {
        // Setup the session cookie
        session_name(APP_NAME);
        $session_params = session_get_cookie_params();
        session_set_cookie_params(
            $session_params['lifetime'], ROOT
        );
    }

}
