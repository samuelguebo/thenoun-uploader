<?php namespace Thenoun\Controllers;

use Thenoun\Utils\FileManager;

/**
 * Controller handling file upload
 */
class UploadController extends AbstractController {

	/**
	 * Handled upload of files
	 *
	 * @param mixed $request
	 * @return void
	 */
	public function upload( $request ) {
		header( "Content-Type: Application/json" );
		// Handle error
		$error_not_found = "An error occured during upload. Please try again.";
		$files = $_FILES;
		$data = filter_input_array( INPUT_POST );
		if ( !isset( $data ) ) {
			$message['status'] = 200;
			$message['message'] = $error_not_found;
			echo json_encode( $message );
			die();
		}

		$response = FileManager::upload( $data );
		if ( $response != false ) {
			// If there are no errors
			$message['status'] = 200;
			$message['message'] = "success";
			$message['icon'] = $response;
		} else {
			$message['status'] = 400;
			$message['message'] = $error_not_found;
		}

		echo json_encode( $message );
		die();
	}
}
