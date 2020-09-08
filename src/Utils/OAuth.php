<?php namespace Thenoun\Utils;

use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;

/**
 * OAuth interacting with MediaWiki-based API
 * this its built-in OAuth extension.
 */
class OAuth {
	private $mwOAuthUrl = OAUTH_MWURI;
	private $gUserAgent = OAUTH_UA;
	private $apiUrl = OAUTH_MWURI . "/w/api.php";
	private $gConsumerKey = OAUTH_KEY;
	private $gConsumerSecret = OAUTH_SECRET;
	private $gTokenSecret;
	private $gTokenKey;
	private $errorCode = 200;

	/**
	 * Printing arbitrary data
	 * in the browser console
	 * @param mixed $data
	 * @return void
	 */
	public static function log( $data ) {
		// Convert array and object to JSON text
		if ( is_object( $data ) || is_array( $data ) ) {
			$data = json_encode( $data );
		}
		echo ( "<script>console.log('" . $data . "')</script>" );
	}

	/**
	 * Handle a callback to fetch the access token
	 * @return void
	 */
	public function fetchAccessToken() {
		$conf = new ClientConfig( $this->mwOAuthUrl . '/w/index.php?title=Special:OAuth' );
		$conf->setConsumer( new Consumer(
			$this->gConsumerKey,
			$this->gConsumerSecret
		) );
		$client = new Client( $conf );
		list( $authUrl, $token ) = $client->initiate();

		// Save the access token
		Router::setcookie( 'tokenKey', $this->gTokenKey = $token->key );
		Router::setcookie( 'tokenSecret', $this->gTokenSecret = $token->secret );
	}

	/**
	 * Setup the session cookie
	 * @return void
	 */
	private function updateSessionToken() {
		// Load the user token (request or access) from the session
		$this->gTokenKey = '';
		$this->gTokenSecret = '';
		session_start();
		// Logger::log(array("in updateSessionToken", $_SESSION));
		if ( isset( $_SESSION['tokenKey'] ) ) {
			$this->gTokenKey = $_SESSION['tokenKey'];
			$this->gTokenSecret = $_SESSION['tokenSecret'];
		}
		session_write_close();

		// Fetch the access token if this is the callback from requesting authorization
		$oauth_verifier = filter_input( INPUT_GET, 'oauth_verifier' );
		if ( isset( $oauth_verifier ) && $oauth_verifier ) {
			$this->fetchAccessToken();
		}
	}
}
