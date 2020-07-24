<?php
namespace Thenoun\Config;
/**
 * Configuration file storing OAUTH settings
 * and other useful information.
 * Direct access is disabled
 */

if (!defined('ROOT')) {
    // Send 403 Forbidden response.
    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    // Kill the script.
    exit;
}

define('OAUTH_MWURI', 'https://commons.wikimedia.org');
define('OAUTH_CALLBACK_URL', 'http://localhost:5000/auth/mediawiki/callback');
define('OAUTH_KEY', '79ea52b320da444132d4ee4d28fd3810');
define('OAUTH_SECRET', 'd0e66fb2a0f655098ba586cfea7a43ee5fd4322f');
define('APP_NAME', 'The Noun Uploader');
define('APP_DESCRIPTION', 'Icon uploader for Wikimedia Commons');
define('APP_SLOGAN', 'Upload <a href="https://thenounproject.com/">thenoun icons</a> to Wikimedia Commons, a repository with millions of free-use images!');
define('OAUTH_UA', APP_NAME);

// Define routes
define('ROUTES', array(
    /**
     * Home endpoints
     */
    array(
        'endpoint' => '/',
        'controller' => 'HomeController',
        'method' => 'index',
        'protected' => false,
    ),
    array(
        'endpoint' => '/test',
        'controller' => 'HomeController',
        'method' => 'test',
        'protected' => false,
    ),
    /**
     * Upload endpoints
     */
    array(
        'endpoint' => '/upload',
        'controller' => 'UploadController',
        'method' => 'upload',
        'protected' => true,
    ),
    /**
     * Authentication endpoints
     */
    array(
        'endpoint' => '/login',
        'controller' => 'AuthController',
        'method' => 'login',
        'protected' => false,
    ),
    array(
        'endpoint' => '/oauth-callback',
        'controller' => 'AuthController',
        'method' => 'callback',
        'protected' => false,
    ),
    array(
        'endpoint' => '/logout',
        'controller' => 'AuthController',
        'method' => 'logout',
        'protected' => false,
    ),
));