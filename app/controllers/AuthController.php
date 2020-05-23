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
        $mediawiki = new Mediawiki();
        $mediawiki->doAuthorizationRedirect();
    }

    /**
     * Oauth call back handler
     */
    public function callback($request)
    {   
        $mediawiki = new Mediawiki();
        $mediawiki->doEdit();
    }

}
