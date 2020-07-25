<?php namespace Thenoun\Controllers;

/**
 * Abstract class for Controllers
 * it can be extended to create
 * additional controllers
 */
abstract class AbstractController {
	public function __construct() {
	}

	/**
	 * Generic middleware shared accross
	 * all child classes
	 *
	 * @param mixed $route
	 * @param mixed $request
	 * @return void
	 */
	public function middleWare( $route, $request ) {
		// Check wether wether user is logged in or not
		if ( ( $route['protected'] ) ) {
			if ( !AuthController::isLoggedIn() ) {
				AuthController::unauthorized( $request );
				exit();
			}
		}
	}

}
