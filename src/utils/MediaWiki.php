<?php namespace Thenoun\Utils;

use Thenoun\Utils\Router;
/**
 * Oauth mechanism largely inspired by Brad Jorsch's approach
 * See <https://tools.wmflabs.org/oauth-hello-world/>
 * TODO: Refactor and reduce code duplication
 */

class MediaWiki
{
    private $mwOAuthUrl = OAUTH_MWURI;
    private $gUserAgent = OAUTH_UA;
    private $apiUrl = OAUTH_MWURI . "/w/api.php";
    private $gConsumerKey = OAUTH_KEY;
    private $gConsumerSecret = OAUTH_SECRET;
    private $gTokenSecret;
    private $gTokenKey;
    private $errorCode = 200;

    /**
     * Request authorization
     * @return void
     */
    public function doAuthorizationRedirect()
    {
        $this->updateSessionToken();

        if (null!= Router::getCookie('tokenKey')) {
            $this->gTokenKey = Router::getCookie('tokenKey');
            $this->gTokenSecret =Router::getCookie('tokenSecret');
        }

        // First, we need to fetch a request token.
        // The request is signed with an empty token secret and no token key.
        $this->gTokenSecret = '';
        $url = $this->mwOAuthUrl . '/w/index.php?title=Special:OAuth/initiate';
        $url .= strpos($url, '?') ? '&' : '?';
        $url .= http_build_query(array(
            'format' => 'json',

            // OAuth information
            'oauth_callback' => 'oob', // Must be "oob" or something prefixed by the configured callback URL
            'oauth_consumer_key' => $this->gConsumerKey,
            'oauth_version' => '1.0',
            'oauth_nonce' => md5(microtime() . mt_rand()),
            'oauth_timestamp' => time(),

            // We're using secret key signatures here.
            'oauth_signature_method' => 'HMAC-SHA1',
        ));
        $signature = $this->signRequest('GET', $url);
        $url .= "&oauth_signature=" . urlencode($signature);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch, CURLOPT_USERAGENT, $this->gUserAgent);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        if (!$data) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Curl error: ' . htmlspecialchars(curl_error($ch));
            exit(0);
        }
        curl_close($ch);
        $token = json_decode($data);
        if (is_object($token) && isset($token->error)) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Error retrieving token: ' . htmlspecialchars($token->error) . '<br>' . htmlspecialchars($token->message);
            exit(0);
        }
        if (!is_object($token) || !isset($token->key) || !isset($token->secret)) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Invalid response from token request';
            exit(0);
        }

        // Now we have the request token, we need to save it for later.
        session_start();
        $_SESSION['tokenKey'] = $token->key;
        $_SESSION['tokenSecret'] = $token->secret;
        $_SESSION['loggedIn'] = true;

        session_write_close();

        // Then we send the user off to authorize
        $url = $this->mwOAuthUrl . '/w/index.php?title=Special:OAuth/authorize';
        $url .= strpos($url, '?') ? '&' : '?';
        $url .= http_build_query(array(
            'oauth_token' => $token->key,
            'oauth_consumer_key' => $this->gConsumerKey,
        ));

        header("Location: $url");
        echo 'Please see <a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($url) . '</a>';
    }

    /**
     * Utility function to sign a request
     *
     * @param string $method Generally "GET" or "POST"
     * @param string $url URL string
     * @param array $params Extra parameters for the Authorization header or post
     *     data (if application/x-www-form-urlencoded).
     * @return string Signature
     */
    public function signRequest($method, $url, $params = array())
    {
        $parts = parse_url($url);

        // We need to normalize the endpoint URL
        $scheme = isset($parts['scheme']) ? $parts['scheme'] : 'http';
        $host = isset($parts['host']) ? $parts['host'] : '';
        $port = isset($parts['port']) ? $parts['port'] : ($scheme == 'https' ? '443' : '80');
        $path = isset($parts['path']) ? $parts['path'] : '';
        if (($scheme == 'https' && $port != '443') ||
            ($scheme == 'http' && $port != '80')
        ) {
            // Only include the port if it's not the default
            $host = "$host:$port";
        }

        // Also the parameters
        $pairs = array();
        parse_str(isset($parts['query']) ? $parts['query'] : '', $query);
        $query += $params;
        unset($query['oauth_signature']);
        if ($query) {
            $query = array_combine(
                // rawurlencode follows RFC 3986 since PHP 5.3
                array_map('rawurlencode', array_keys($query)),
                array_map('rawurlencode', array_values($query))
            );
            ksort($query, SORT_STRING);
            foreach ($query as $k => $v) {
                $pairs[] = "$k=$v";
            }
        }

        $toSign = rawurlencode(strtoupper($method)) . '&' .
        rawurlencode("$scheme://$host$path") . '&' .
        rawurlencode(join('&', $pairs));

        $key = rawurlencode($this->gConsumerSecret) . '&' . rawurlencode($this->gTokenSecret);

        return base64_encode(hash_hmac('sha1', $toSign, $key, true));
    }

    /**
     * Perform a generic edit
     * @return void
     */
    public function doEdit()
    {
        $this->updateSessionToken();
        $ch = null;

        // First fetch the username
        $res = $this->makeRequest(array(
            'format' => 'json',
            'action' => 'query',
            'meta' => 'userinfo',
        ), $ch);

        if (isset($res->error->code) && $res->error->code === 'mwoauth-invalid-authorization') {
            // We're not authorized!
            echo 'You haven\'t authorized this application yet! Go <a href="' . htmlspecialchars($_SERVER['SCRIPT_NAME']) . '?action=authorize">here</a> to do that.';
            echo '<hr>';
            return;
        }

        if (!isset($res->query->userinfo)) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Bad API response: <pre>' . htmlspecialchars(var_export($res, 1)) . '</pre>';
            exit(0);
        }
        if (isset($res->query->userinfo->anon)) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Not logged in. (How did that happen?)';
            exit(0);
        }
        //$page = 'User talk:' . $res->query->userinfo->name;
        $page = "User:African_Hope/TheNounProject";
        // Next fetch the edit token
        $res = $this->makeRequest(array(
            'format' => 'json',
            'action' => 'tokens',
            'type' => 'edit',
        ), $ch);
        if (!isset($res->tokens->edittoken)) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Bad API response: <pre>' . htmlspecialchars(var_export($res, 1)) . '</pre>';
            exit(0);
        }
        $token = $res->tokens->edittoken;
        // Now perform the edit
        
        $res = $this->makeRequest( array(
        'format' => 'json',
        'action' => 'edit',
        'title' => $page,
        'section' => 'new',
        'sectiontitle' => 'Test '. date("m.d.y @ h:i"),
        'text' => 'This message was posted using the OAuth Hello World application, and should be seen as coming from yourself. To revoke this application\'s access to your account, visit [[:m:Special:OAuthManageMyGrants]]. ~~~~',
        'summary' => 'Hello, world Hello from OAuth!',
        'watchlist' => 'nochange',
        'token' => $token,
        ), $ch );
        

        echo 'API edit result: <pre>' . htmlspecialchars(var_export($res, 1)) . '</pre>';
        echo '<hr>';
    }

    /**
     * Send an API query with OAuth authorization
     *
     * @param array $post Post data
     * @param object $ch Curl handle
     * @return array API results
     */
    private function makeRequest($post, &$ch = null)
    {
        $headerArr = array(
            // OAuth information
            'oauth_consumer_key' => $this->gConsumerKey,
            'oauth_token' => $this->gTokenKey,
            'oauth_version' => '1.0',
            'oauth_nonce' => md5(microtime() . mt_rand()),
            'oauth_timestamp' => time(),

            // We're using secret key signatures here.
            'oauth_signature_method' => 'HMAC-SHA1',
        );
        $signature = $this->signRequest('POST', $this->apiUrl, $post + $headerArr);
        $headerArr['oauth_signature'] = $signature;

        $header = array();
        foreach ($headerArr as $k => $v) {
            $header[] = rawurlencode($k) . '="' . rawurlencode($v) . '"';
        }
        $header = 'Authorization: OAuth ' . join(', ', $header);

        if (!$ch) {
            $ch = curl_init();
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch, CURLOPT_USERAGENT, $this->gUserAgent);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        if (!$data) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Curl error: ' . htmlspecialchars(curl_error($ch));
            exit(0);
        }
        $ret = json_decode($data);
        if ($ret === null) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Unparsable API response: <pre>' . htmlspecialchars($data) . '</pre>';
            exit(0);
        }
        return $ret;
    }

    /**
     * Setup the session cookie
     * @return void
     */
    private function updateSessionToken()
    {   

        // Load the user token (request or access) from the session
        $this->gTokenKey = '';
        $this->gTokenSecret = '';
        session_start();
        // Logger::log(array("in updateSessionToken", $_SESSION));
        if (isset($_SESSION['tokenKey'])) {
            $this->gTokenKey = $_SESSION['tokenKey'];
            $this->gTokenSecret = $_SESSION['tokenSecret'];
        }
        session_write_close();

        // Fetch the access token if this is the callback from requesting authorization
        if (isset($_GET['oauth_verifier']) && $_GET['oauth_verifier']) {
            $this->fetchAccessToken();
        }
    }

    /**
     * Handle a callback to fetch the access token
     * @return void
     */
    public function fetchAccessToken()
    {
        $url = $this->mwOAuthUrl . '/w/index.php?title=Special:OAuth/token';
        $url .= strpos($url, '?') ? '&' : '?';

        $url .= http_build_query(array(
            'format' => 'json',
            'oauth_verifier' => $_GET['oauth_verifier'],

            // OAuth information
            'oauth_consumer_key' => $this->gConsumerKey,
            'oauth_token' => $this->gTokenKey,
            'oauth_version' => '1.0',
            'oauth_nonce' => md5(microtime() . mt_rand()),
            'oauth_timestamp' => time(),

            // We're using secret key signatures here.
            'oauth_signature_method' => 'HMAC-SHA1',
        ));

        $signature = $this->signRequest('GET', $url);
        $url .= "&oauth_signature=" . urlencode($signature);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch, CURLOPT_USERAGENT, $this->gUserAgent);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        if (!$data) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Curl error: ' . htmlspecialchars(curl_error($ch));
            exit(0);
        }
        curl_close($ch);
        $token = json_decode($data);

        if (is_object($token) && isset($token->error)) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Error retrieving token: ' . htmlspecialchars($token->error) . '<br>' . htmlspecialchars($token->message);
            exit(0);
        }
        if (!is_object($token) || !isset($token->key) || !isset($token->secret)) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Invalid response from token request';
            exit(0);
        }

        // Save the access token
        Router::setcookie('tokenKey', $this->gTokenKey = $token->key);
        Router::setcookie('tokenSecret', $this->gTokenSecret = $token->secret);

    }

    /**
     * Get details about a user
     */
     /**
     * Perform a generic edit
     * @return void
     */
    public function getProfile()
    {
        $this->updateSessionToken();
        $ch = null;

        // First fetch the username
        $res = $this->makeRequest(array(
            'format' => 'json',
            'action' => 'query',
            'meta' => 'userinfo',
        ), $ch);

        if (isset($res->error->code) && $res->error->code === 'mwoauth-invalid-authorization') {
            // We're not authorized!
            echo 'You haven\'t authorized this application yet! Go <a href="' . htmlspecialchars($_SERVER['SCRIPT_NAME']) . '?action=authorize">here</a> to do that.';
            echo '<hr>';
            return;
        }

        if (!isset($res->query->userinfo)) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Bad API response: <pre>' . htmlspecialchars(var_export($res, 1)) . '</pre>';
            exit(0);
        }
        if (isset($res->query->userinfo->anon)) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Not logged in. (How did that happen?)';
            exit(0);
        }
        
        return $res;
    
    }

    /**
     * For now, only edit a sandbox on Wikimedia Commons
     * Once the test are conclusive, the logic will
     * be replaced with the actual upload process
     *
     * @param  mixed $icon
     * @return string / error
     */
    public function uploadFile($icon)
    {
        $this->updateSessionToken();
        $ch = null;

        // 1. fetch the username
        $res = $this->makeRequest(array(
            'format' => 'json',
            'action' => 'query',
            'meta' => 'userinfo',
        ), $ch);

        if (isset($res->error->code) && $res->error->code === 'mwoauth-invalid-authorization') {
            // We're not authorized!
            throw new Exception('You haven\'t authorized this application yet!');
        }

        if (!isset($res->query->userinfo)) {
            throw new Exception("$this->errorCode Internal Server Error");
        }
        if (isset($res->query->userinfo->anon)) {
            throw new Exception("HTTP/1.1 $this->errorCode Internal Server Error");
        }
        
        $page = "User:African_Hope/TheNounProject";
        
        // 2. fetch the edit token
        $res = $this->makeRequest(array(
            'format' => 'json',
            'action' => 'tokens',
            'type' => 'edit',
        ), $ch);
        if (!isset($res->tokens->edittoken)) {
            header("HTTP/1.1 $this->errorCode Internal Server Error");
            echo 'Bad API response: <pre>' . htmlspecialchars(var_export($res, 1)) . '</pre>';
            exit(0);
        }
        $token = $res->tokens->edittoken;
        
        // 3. Perform the edit
        /*
        $res = $this->makeRequest( array(
        'format' => 'json',
        'action' => 'edit',
        'title' => $page,
        'section' => 'new',
        'sectiontitle' => $icon->title,
        'text' => $icon->wikicode,
        'summary' => 'Bot:CivBot/ [[Commons:Batch uploading/TheNounProject|Batch uploading/TheNounProject]]',
        'watchlist' => 'nochange',
        'token' => $token,
        ), $ch );

        */

        $icon->path  = "https://commons.wikimedia.org/wiki/";
        $icon->path .= str_replace(' ', '_', $icon->title);

        return $icon;
    }


}
