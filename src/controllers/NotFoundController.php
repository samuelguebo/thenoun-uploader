<?php
/**
 * REST Controller for Job entities
 * and their relevant endpoints
 */
class NotFoundController extends Controller{
    /**
     * Return error message
     * in Json format
     */
    public static function print($request=null) {
        $error_not_found = "The endpoint does not exist";
        
        header("Content-type: application/json");
        $message = array();
        $message['status'] = 400;
        $message['message'] = $error_not_found;

        echo json_encode($message);
    }
}
?>