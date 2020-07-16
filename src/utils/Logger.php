<?php namespace Thenoun\Utils;
/**
 * Router responsible for redirecting
 * incoming request and mapping them
 * to the correct controller
 */
class Logger
{
    /**
     * Printing arbitrary data
     * in the browser console
     * @return void
     */
    public static function log($data)
    {
        // Convert array and object to JSON text
        if (is_object($data) || is_array($data)) {
            $data = json_encode($data);
        }
        echo ("<script>console.log('" . $data . "')</script>");

    }
}
