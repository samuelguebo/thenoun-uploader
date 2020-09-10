<?php

namespace Thenoun\Utils;

use Thenoun\Controllers\NotFoundController;

/**
 * Router responsible for redirecting
 * incoming request and mapping them
 * to the correct controller
 */
class Router {

	private $routes;
	private $request;
	public static $session;

	/**
	 * Constructor
	 * @param mixed $routes
	 * @return void
	 */
	public function __construct( $routes ) {
		$this->routes = $routes;
		$this->request = $_SERVER['REQUEST_URI'];
	}

	/**
	 * Rerouting requests
	 */
	public function dispatch() {
		foreach ( $this->routes as $route ) {
			$endpoint = explode( "?", $this->request )[0];
			if ( $route['endpoint'] === $endpoint ) {
				// If class exists, use it
				$class = "Thenoun\\Controllers\\" . $route['controller'];

				if ( class_exists( $class ) ) {
					$controller = ( new $class() );
					$controller->middleWare( $route, $this->request );
					// Check and select the method to call
					if ( method_exists( $controller, $route['method'] ) ) {
						$method = $route['method'];
						// passing the request into the method
						$controller->$method( $this->request );
						exit();
					}
				}
			}

		}

		NotFoundController::print();
	}

	/**
	 * Setup the session cookie
	 * @param mixed $key
	 * @return void
	 */
	public static function getCookie( $key ) {
		if ( !isset( $_SESSION ) ) {
			session_start();
		}
		self::$session = $_SESSION;
		// session_write_close();
		// Logger::log( [ "in getCookie", $_SESSION ] );
		if ( isset( self::$session[$key] ) ) {
			return self::$session[$key];
		}
	}

	/**
	 * Setup the session cookie
	 * @return array
	 */
	public static function getSession() {
		session_start();
		$session = $_SESSION;
		session_write_close();
		return $session;
	}

	/**
	 * Setup the session cookie
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	public static function setCookie( $key, $value ) {
		if ( !isset( $_SESSION ) ) {
			session_start();
		}
		$_SESSION[$key] = $value;
		// Logger::log( [ "in setCookie", $_SESSION ] );
		// session_write_close();
	}

	/**
	 * resetSession
	 *
	 * @return void
	 */
	public static function resetSession() {
		if ( !isset( $_SESSION ) ) {
			session_destroy();
		}
	}
}
