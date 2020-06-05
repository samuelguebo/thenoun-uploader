<?php
/**
 * Abstract class for Controllers
 * it can be extended to create
 * additional REST controllers
 */
class Controller{
    public function __construct () {
        
    }

    /**
     * Generic middleware shared accross
     * all child classes
     */
    public function middleWare($route) {

        // Check wether wether user is logged in or not
        //Logger::log("Entered " . get_class($this) . " middleware");
        
        if(($route['protected'])){
            AuthController::unauthorized($request);
            exit();
        }
    }


}
?>