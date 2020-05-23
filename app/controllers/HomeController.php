<?php
/**
 * Controller handling homepage
 */
class HomeController extends Controller{
   /**
     * Rest endpoint for route `/`
     * it matches GET requests
     */
    public function index($request=null) {
        require(ROOT . "/app/views/index.php");
    }
}
?>