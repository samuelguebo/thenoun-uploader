<?php
/**
 * Router responsible for redirecting
 * incoming request and mapping them
 * to the correct controller
 */
class Router {

    private $controller;

    /**
     * Constructor
     */
    public function __construct() {
        $this->request = new Request();
        $this->request->parse();
    }
    /**
     * Rerouting requests
     */
    public function  dispatch() {
        
        if(isset($this->request->controller)) {
            $endpoint = ucfirst($this->request->controller);
            $classname = $endpoint . "Controller";
            
                            
            // If class exists, use it
            if ( class_exists( $classname ) ) {
                $controller = new $classname();
                
                // Check and select the method to call
                $method = strtolower($this->request->method);
                if(method_exists($controller, $method)) {

                    // Handle CORS issues
                    header("Access-Control-Allow-Origin: *");
                    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
                    // passing the request into the method
                    $controller->$method($this->request);
                    return;
                } 
                
            }

        } 
        NotFoundController::print();
    }
}
?>