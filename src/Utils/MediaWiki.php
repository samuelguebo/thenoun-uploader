<?php namespace Thenoun\Utils;

use CURLFile;
use Exception;
use MediaWiki\OAuthClient\Token;
use Thenoun\Config\Settings;

/**
 * Oauth mechanism largely inspired by Brad Jorsch's approach
 * See <https://tools.wmflabs.org/oauth-hello-world/>
 * TODO: Refactor and reduce code duplication
 * consider https://bitbucket.org/magnusmanske/magnustools/src/master/classes/OAuth.php
 */

class MediaWiki {
	private $gTokenSecret;
	private $gTokenKey;
	private $errorCode = 200;

	/**
	 * Edit a sandbox on Wikimedia Commons
	 *
	 * @param mixed $title Name space
	 * @param mixed $wikicode text in special markup (wikicode)
	 * @return string / error
	 */
	public function editPage( $title, $wikicode ) {
		try {
			$client = ( new OAuth() )->getClient();
			$accessToken = new Token(
				Router::getCookie( 'accessToken' ),
				Router::getCookie( 'accessSecret' )
			);

			// Get edit token
			$token = json_decode( $client->makeOAuthCall(
				$accessToken,
				Settings::$OAUTH_MWURI . "/w/api.php" . '?action=query&meta=tokens&format=json'
			) )->query->tokens->csrftoken;

			// Perform the edit
			$params = [
				'format' => 'json',
				'action' => 'edit',
				'title' => $title,
				'text' => $wikicode,
				'summary' => 'Bot:CivBot/ [[Commons:Batch uploading/TheNounProject|Batch uploading/TheNounProject]]',
				'watchlist' => 'nochange',
				'token' => $token,
			];

			// Get response
			$res = json_decode( $client->makeOAuthCall(
				$accessToken,
				Settings::$OAUTH_MWURI . "/w/api.php",
				true,
				$params
			) );

			return $res;
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * Push icon file to Wikimedia Commons
	 * while avoiding duplicates
	 * @param mixed $icon
	 * @return string / error
	 */
	public function uploadFile( $icon ) {
		$client = ( new OAuth() )->getClient();
		$accessToken = new Token(
			Router::getCookie( 'accessToken' ),
			Router::getCookie( 'accessSecret' )
		);

		// Get CSRF token
		$token = json_decode( $client->makeOAuthCall(
			$accessToken,
			Settings::$OAUTH_MWURI . "/w/api.php" . '?action=query&meta=tokens&format=json'
		) )->query->tokens->csrftoken;

		// Prepare to send file
		$file = new CURLFile( realpath( $icon->path ), 'image/svg+xml', $icon->title );

		// Set up parameters, stripping off "File:"
		$params = [
		'format' => 'json',
		'action' => 'upload',
		'filename' => $icon->title,
		'text' => $icon->wikicode,
		'file' => $file,
		'token' => $token,
		"ignorewarnings" => 1
		];

		// Perform the upload
		$res = json_decode( $client->makeOAuthCall(
			$accessToken,
			Settings::$OAUTH_MWURI . "/w/api.php",
			true,
			$params
		) );

		if ( isset( $res->error ) ) {
			// throw new Exception('An error occured while uploading');
			throw new Exception( json_encode( $res->error->info ) );
		}

		// Updating icon path
		$icon->path  = "https://commons.wikimedia.org/wiki/";
		$icon->path .= str_replace( ' ', '_', $icon->title );

		return $icon;
	}

	/**
	 * isFileExistent
	 *
	 * @param mixed $file
	 * @return void
	 */
	public function isFileExistent( $file ) {
		try {
			$response = file_get_contents( Settings::$OAUTH_MWURI . "/w/api.php" . "?action=query&prop=revisions&titles=" . urlencode( $file->title ) . "&rvslots=*&rvprop=content&format=json" );
			return !array_key_exists( "-1", json_decode( $response, true )['query']['pages'] );

		} catch ( Exception $e ) {
			return true;
		}
	}
}
