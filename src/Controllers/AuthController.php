<?php namespace Thenoun\Controllers;

use Thenoun\Utils\MediaWiki;
use Thenoun\Utils\Router;

/**
 * Authentication mechanism
 */
class AuthController extends Controller {
	/**
	 * Login route
	 *
	 * @param mixed $request
	 * @return void
	 */
	public function login( $request ) {
		$mediawiki = new MediaWiki();
		$mediawiki->doAuthorizationRedirect();
	}

	/**
	 * Oauth call back handler
	 *
	 * @param mixed $request
	 * @return void
	 */
	public function callback( $request ) {
		$mediawiki = new MediaWiki();
		$user = $mediawiki->getProfile();

		header( "Location: /" );
	}

	/** unauthorized
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
