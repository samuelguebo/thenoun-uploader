<?php namespace Thenoun\Controllers;

use Thenoun\Models\Icon;
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
        }
        
        $files = $_FILES;
        $metadata = $_POST;
        // Create temporary directory if inexistent
        $tmp_dir = ROOT . '/src/tmp';
        if (!file_exists($tmp_dir)) {
            mkdir($tmp_dir, 0777, true);
        }
        
        // Move files in it
        $filesProcessed = array();
        for ($i=0; $i < count($files) ; $i++) { 
            // move file to temporary directory
            move_uploaded_file($file['tmp_name'], $tmp_dir. "/" . $file['name']);

            // prepare submission to Mediawiki API
            $data = $metadata[$i];
            $icon = new Icon($data['title'], $data['author'], $data['title']);
            $filesProcessed[] = $icon;
        }
        
        

        $message['status'] = 200;
        $message['files'] = $filesProcessed;
        $message['others'] = $_POST;
        echo json_encode($message);

        
    }
}
