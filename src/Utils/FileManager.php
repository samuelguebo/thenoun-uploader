<?php namespace Thenoun\Utils;

use Exception;
use Thenoun\Models\Icon;

/**
 * Utility responsible for handling
 * files operations and interacting
 * with Mediawiki API
 */
class FileManager {
	/**
	 * Uploading file by leveraging
	 * the Mediawiki helper
	 *
	 * @param mixed $data
	 * @return void
	 */
	public static function upload( $data ) {
		try {
			// Create temporary directory if inexistent
			$tmp_dir = ROOT . '/tmp';
			if ( !file_exists( $tmp_dir ) ) {
				mkdir( $tmp_dir, 0777, true );
			}

			$icon = json_decode( $data["icon"] );

			// Create file in temporary directory
			$path = $tmp_dir . "/" . $icon->filename;
			file_put_contents( $path, $icon->content );

			// Prepare submission to Mediawiki API
			$wiki = new MediaWiki;
			$icon = new Icon( $icon->title, $icon->author, $icon->wikicode, $path );

			// Check whether file already exists
			if ( $wiki->isFileExistent( $icon ) ) {
				throw new Exception( "File already exists" );
			}

			$result = $wiki->uploadFile( $icon );
			if ( $result != false ) {
				// Insert wikicode in page
				$wiki->editPage( $result );
			}
			// if there are still no errors, remove the file from folder
			unlink( $path );
			return $result;

		}catch ( Exception $e ) {
			return $e->getMessage();
		}
	}
}
