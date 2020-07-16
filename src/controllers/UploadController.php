<?php namespace Thenoun\Controllers;
/**
 * Controller handling file upload
 */
class UploadController extends Controller
{

    /**
     * Handled upload of files
     */
    public function upload($request)
    {   
        header("Content-Type: Application/json");
        
        // Handle error
        $error_not_found = "An error occured during upload. Please try again.";
        if(!isset($_FILES)){
            $message['status'] = 200;
            $message['message'] = $error_not_found;
            echo json_encode($message);
        }
        
        // Process files, create temporary directory if inexistent
        $tmp_dir = ROOT . '/src/tmp';
        if (!file_exists($tmp_dir)) {
            mkdir($tmp_dir, 0777, true);
        }
        $files = $_FILES;
        $filesHandled = array();
        foreach($files as $file){
            // move file to temporary directory
            move_uploaded_file($file['tmp_name'], $tmp_dir. "/" . $file['name']);
            $filesHandled[] = $file['name'];
        }
        
        $message['status'] = 200;
        $message['files'] = $filesHandled;
        echo json_encode($message);
    }
}
