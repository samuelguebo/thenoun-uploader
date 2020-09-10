<?php namespace Thenoun\Controllers;

use Thenoun\Utils\OAuth;

/**
 * Controller handling homepage
 */
class HomeController extends AbstractController {
	/**
	 * Rest endpoint for route `/`
	 * it matches GET requests
	 * @param mixed $request
	 * @return void
	 */
	public function index( $request = null ) {
		if ( AuthController::isLoggedIn() ) {
			$oauth = new OAuth();
			$user = $oauth->getProfile()->query->userinfo;
			require ROOT . "/src/Views/index.php";
		} else {
			require ROOT . "/src/Views/logged-out.php";
		}
	}

	/**
	 * Handled upload of files
	 *
	 * @param mixed $request
	 * @return void
	 */
	public function upload( $request ) {
		header( "Content-Type: Application/json" );

		$response = [];
		$response['text'] = [ "echo 'Lorem ipsum dolor", $request ];
		echo json_encode( $response );
	}
}
