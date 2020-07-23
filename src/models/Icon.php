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
    
        
    /**
     * Default constructor
     *
     * @param  string $title
     * @param  string $author
     * @param  string $wikicode
     * @return void
     */
    function __constructor($title, $author, $wikicode) {
        $this->title  = $title;
        $this->author = $author;
        $this->author = $wikicode;
    }
}
