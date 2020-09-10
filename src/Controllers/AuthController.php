<?php namespace Thenoun\Controllers;

use Exception;
use Thenoun\Utils\OAuth;
use Thenoun\Utils\Router;

/**
 * Authentication mechanism
 */
class AuthController extends AbstractController {
	/**
	 * Login route
	 *
	 * @param mixed $request
	 * @return void
	 */
	public function login( $request ) {
		try {
			$oauth = new OAuth();
			$oauth->doAuthorizationRedirect();
		} catch ( Exception $e ) {
			echo $e->getMessage();
		}
	}

	/**
	 * Oauth call back handler
	 *
	 * @param mixed $request
	 * @return void
	 */
	public function callback( $request ) {
		$oauth = new OAuth();
		try {
			$oauth->getAccessToken();
			header( "Location: /" );
		} catch ( Exception $e ) {
			echo $e->getMessage();
		}
	}

	/**
	 * unauthorized
	 *
	 * @param mixed $request
	 * @return void
	 */
	public static function unauthorized( $request ) {
		require ROOT . "/src/views/logged-out.php";
	}

	/**
	 * Logout route
	 *
	 * @param mixed $request
	 * @return void
	 */
	public function logout( $request ) {
		Router::setCookie( 'loggedIn', false );
		Router::resetSession();
		header( 'Location: /' );
	}

	/**
	 * Indicate whether the current user
	 * is logged in
	 *
	 * @return void
	 */
	public static function isLoggedIn() {
		$isLoggedIn = Router::getCookie( 'loggedIn' );
		return ( true === $isLoggedIn );
	}

}
