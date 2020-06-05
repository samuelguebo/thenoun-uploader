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
        $user = $mediawiki->getProfile();

        header("Location: /");
    }

    public static function unauthorized($request){

        require ROOT . "/src/views/logged-out.php";
    }

    /**
     * Logout route
     */
    public function logout($request)
    {   
        Router::setCookie('loggedIn', false);
        header('Location: /');
    }
    /**
     * Indicate whether the current user
     * is logged in
     */
    public static function isLoggedIn(){
        $isLoggedIn = Router::getCookie('loggedIn');
        return (true === $isLoggedIn);
    }

}
