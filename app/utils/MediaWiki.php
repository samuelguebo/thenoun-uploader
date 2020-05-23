<?php
/**
 * Authentication mechanism largely inspired
 * by Brad Jorsch's OAUTH approach
 * See <https://tools.wmflabs.org/oauth-hello-world/>
 */

class Mediawiki
{
    private $mwOAuthUrl = OAUTH_MWURI;
    private $gUserAgent = OAUTH_UA;
    private $apiUrl = OAUTH_MWURI . "/w/api.php";
    private $gConsumerKey = OAUTH_KEY;
    private $gConsumerSecret = OAUTH_SECRET;
    private $gTokenSecret;
    private $gTokenKey;
    private $errorCode;

    /**
     * Request authorization
     * @return void
     */
    public function doAuthorizationRedirect()
    {
        session_start();
        if (isset($_SESSION['tokenKey'])) {
            $this->gTokenKey = $_SESSION['tokenKey'];
            $this->gTokenSecret = $_SESSION['tokenSecret'];
        }
        session_write_close();

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
            header("HTTP/1.1 $errorCode Internal Server Error");
            echo 'Curl error: ' . htmlspecialchars(curl_error($ch));
            exit(0);
        }
        curl_close($ch);
        $token = json_decode($data);
        if (is_object($token) && isset($token->error)) {
            header("HTTP/1.1 $errorCode Internal Server Error");
            echo 'Error retrieving token: ' . htmlspecialchars($token->error) . '<br>' . htmlspecialchars($token->message);
            exit(0);
        }
        if (!is_object($token) || !isset($token->key) || !isset($token->secret)) {
            header("HTTP/1.1 $errorCode Internal Server Error");
            echo 'Invalid response from token request';
            exit(0);
        }

        // Now we have the request token, we need to save it for later.
        session_start();
        $_SESSION['tokenKey'] = $token->key;
        $_SESSION['tokenSecret'] = $token->secret;
        session_write_close();

        // Then we send the user off to authorize
        $url = $this->mwOAuthUrl . '/w/index.php?title=Special:OAuth/authorize';
        $url .= strpos($url, '?') ? '&' : '?';
        $url .= http_build_query(array(
            'oauth_token' => $token->key,
            'oauth_consumer_key' => $this->gConsumerKey,
        ));
        header( "Location: $url" );
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

        var_dump(array(
            '$toSign ' => rawurlencode(strtoupper($method)) . '&' .
            rawurlencode("$scheme://$host$path") . '&' .
            rawurlencode(join('&', $pairs)))
        );

        return base64_encode(hash_hmac('sha1', $toSign, $key, true));
    }

    /**
     * Perform a generic edit
     * @return void
     */
    public function doEdit()
    {
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
            header("HTTP/1.1 $errorCode Internal Server Error");
            echo 'Bad API response: <pre>' . htmlspecialchars(var_export($res, 1)) . '</pre>';
            exit(0);
        }
        if (isset($res->query->userinfo->anon)) {
            header("HTTP/1.1 $errorCode Internal Server Error");
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
            header("HTTP/1.1 $errorCode Internal Server Error");
            echo 'Bad API response: <pre>' . htmlspecialchars(var_export($res, 1)) . '</pre>';
            exit(0);
        }
        $token = $res->tokens->edittoken;

        /*
        var_dump (array(
        'token' => $token,
        'apiURL' => $this->apiUrl,
        ));
         */

        // Now perform the edit
        /*
        $res = $this->makeRequest( array(
        'format' => 'json',
        'action' => 'edit',
        'title' => $page,
        'section' => 'new',
        'sectiontitle' => 'Hello, world',
        'text' => 'This message was posted using the OAuth Hello World application, and should be seen as coming from yourself. To revoke this application\'s access to your account, visit [[:m:Special:OAuthManageMyGrants]]. ~~~~',
        'summary' => 'Hello, world Hello from OAuth!',
        'watchlist' => 'nochange',
        'token' => $token,
        ), $ch );
         */

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
    function makeRequest( $post, &$ch = null ) {

        $headerArr = array(
            // OAuth information
            'oauth_consumer_key' => $this->gConsumerKey,
            'oauth_token' => $this->gTokenKey,
            'oauth_version' => '1.0',
            'oauth_nonce' => md5( microtime() . mt_rand() ),
            'oauth_timestamp' => time(),

            // We're using secret key signatures here.
            'oauth_signature_method' => 'HMAC-SHA1',
        );
        $signature = $this->signRequest( 'POST', $this->apiUrl, $post + $headerArr );
        $headerArr['oauth_signature'] = $signature;

        $header = array();
        foreach ( $headerArr as $k => $v ) {
            $header[] = rawurlencode( $k ) . '="' . rawurlencode( $v ) . '"';
        }
        $header = 'Authorization: OAuth ' . join( ', ', $header );

        if ( !$ch ) {
            $ch = curl_init();
        }
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_URL, $this->apiUrl );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post ) );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $header ) );
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_USERAGENT, $this->gUserAgent );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $data = curl_exec( $ch );
        if ( !$data ) {
            header( "HTTP/1.1 $errorCode Internal Server Error" );
            echo 'Curl error: ' . htmlspecialchars( curl_error( $ch ) );
            exit(0);
        }
        $ret = json_decode( $data );
        if ( $ret === null ) {
            header( "HTTP/1.1 $errorCode Internal Server Error" );
            echo 'Unparsable API response: <pre>' . htmlspecialchars( $data ) . '</pre>';
            exit(0);
        }
        return $ret;
    }


}
