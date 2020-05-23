<?php
/**
 * Rename this file to `settings.php`
 * and make sure it's not accessible
 * to the client
 */

if(!defined('ROOT')) {
    // Send 403 Forbidden response.
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    // Kill the script.
    exit;
}

define('OAUTH_MWURI', 'https://commons.wikimedia.org');
define('OAUTH_CALLBACK_URL', '');
define('OAUTH_KEY', '');
define('OAUTH_SECRET', '');