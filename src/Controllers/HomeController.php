<?php namespace Thenoun\Controllers;

use Thenoun\Utils\MediaWiki;
use Thenoun\Controllers\AuthController;
/**
 * Controller handling homepage
 */
class HomeController extends Controller
{
    /**
     * Rest endpoint for route `/`
     * it matches GET requests
     */
    public function index($request = null)
    {   
        
        if(AuthController::isLoggedIn()){
            $mediawiki = new MediaWiki();
            $user = $mediawiki->getProfile()->query->userinfo;
            require ROOT . "/src/Views/index.php";
        }else{
            require ROOT . "/src/Views/logged-out.php";
        }
        
    }

    /**
     * Testing
     */
    public function test($request)
    {
        header("Content-Type: Application/json");

        $response = array();
        $response['text'] = array("echo 'Lorem ipsum dolor", $request);
        echo json_encode($response);
    }

    /**
     * Handled upload of files
     */
    public function upload($request)
    {
        header("Content-Type: Application/json");

        $response = array();
        $response['text'] = array("echo 'Lorem ipsum dolor", $request);
        echo json_encode($response);
    }
}
