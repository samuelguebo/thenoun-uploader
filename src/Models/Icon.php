<?php
namespace Thenoun\Models;

/**
 * Entity that holds the data for processed
 * and passed to the Mediawiki API
 */
class Icon {
	public $title;
	public $author;
	public $wikicode;
	public $path;

	/**
	 * Default constructor
	 *
	 * @param string $title
	 * @param string $author
	 * @param string $wikicode
	 * @param string $path
	 * @return void
	 */
	public function __construct( $title, $author, $wikicode, $path ) {
		$this->title  = $title;
		$this->author = $author;
		$this->wikicode = $wikicode;
		$this->path   = $path;
	}
}
