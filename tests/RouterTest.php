<?php
require_once dirname(__FILE__) . '/app/utils/Router.php';

class RouterTest extends PHPUnit_Framework_TestCase{

    function testCanCreateRouter() {
        //$router = new Router(null, null);
        $router = '';
        $this->assertEquals('', $router);
    }
}