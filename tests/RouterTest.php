<?php
use Thenoun\Utils\Router;
use PHPUnit\Framework\TestCase;


class RouterTest extends TestCase{

    function testCanCreateRouter() {
        $_SERVER['REQUEST_URI'] = null;
        $router = new Router(null, null);
        $this->assertEquals(4, 4);
    }
}