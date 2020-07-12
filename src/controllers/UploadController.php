<?php
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

        $response = array();
        $response['text'] = array("echo 'Lorem ipsum dolor", $request);
        echo json_encode($response);
    }
}
