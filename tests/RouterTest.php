<?php
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase{

    function testCanCreateRouter() {
        $router = new Router(null, null);
        $this->assertTrue(is_object($router));
    }
}