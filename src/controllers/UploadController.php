<?php namespace Thenoun\Controllers;

use Thenoun\Models\Icon;
use Thenoun\Utils\FileManager;
/**
 * Controller handling file upload
 */
class UploadController extends Controller
{

 
    /**
     * Handled upload of files
     *
     * @param  mixed $request
     * @return void
     */
    public function upload($request)
    {   

        header("Content-Type: Application/json");
        // Handle error
        $error_not_found = "An error occured during upload. Please try again.";
        if(!isset($_FILES) || !isset($_POST)){
            $message['status'] = 200;
            $message['message'] = $error_not_found;
            echo json_encode($message);
            die();
        }
        
        $response = FileManager::upload($_FILES, $_POST);
        if($response != false) {
            // If there are no errors
            $message['status'] = 200;
            $message['message'] = "success";
            $message['url'] = $response;
        }else {
            $message['status'] = 400;
            $message['message'] = $error_not_found;
        }
        
        echo json_encode($message);
        die();
        
    }
}
