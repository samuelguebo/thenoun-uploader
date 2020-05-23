<?php
/**
 * Authentication mechanism
 */
class AuthController extends Controller
{
    /**
     * Login route
     */
    public function login($request)
    {   
        
        $mediawiki = new MediaWiki();
        $mediawiki->doAuthorizationRedirect();
    }

    /**
     * Oauth call back handler
     */
    public function callback($request)
    {   
        $mediawiki = new MediaWiki();
        $mediawiki->doEdit();
    }

}
