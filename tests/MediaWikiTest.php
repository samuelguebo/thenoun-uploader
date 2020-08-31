<?php
use PHPUnit\Framework\TestCase;
use Thenoun\Models\Icon;
use Thenoun\Utils\MediaWiki;

class MediaWikiTest extends TestCase {
	/**
	 * Settings shared across all subtest methods
	 *
	 * @return void
	 */
	protected function setUp() : void {
		define( 'ROOT', dirname( __DIR__ ) );
		require_once ROOT . '/src/Config/settings.php';
	}

	/**
	 * testIsFileExistent
	 *
	 * @covers \MediaWiki\
	 * @return void
	 */
	public function testIsFileExistent() {
		$file = new Icon( 'File:2010-10-10-oderpruch-pl-by-RalfR-23.jpg', null, null, null );
		$this->assertEquals( ( new MediaWiki() )->isFileExistent( $file ), true );
	}
}
