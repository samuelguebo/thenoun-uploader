<?php namespace Thenoun\Controllers;

use Exception;
use Thenoun\Utils\MediaWiki;
use Thenoun\Utils\OAuth;

/**
 * Controller handling homepage
 */
class TestController extends AbstractController {

	/**
	 * Testing
	 * @param mixed $request
	 * @return void
	 */
	public function test( $request ) {
		try {
			$oauth = new OAuth();
			if ( AuthController::isLoggedIn() ) {
				$oauth = new OAuth();
				$mediawiki = new MediaWiki();
				$res = $mediawiki->editPage( 'File:Robot (699) - The Noun Project.svg', '' );
				var_dump( $res );
			} else {
				require ROOT . "/src/Views/logged-out.php";
			}
		} catch ( Exception $e ) {
			echo $e->getMessage();
		}
	}

	/**
	 * Mock upload to mediawiki
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

		// If there are no errors
		$message['status'] = 200;
		$message['message'] = "success";
		$message['files'] = $data;
		print_r( $message );
		// echo json_encode( $message );
		die();
	}
}
