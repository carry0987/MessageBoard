<?php
class GithubOAuth
{
    public static $authorizeURL = 'https://github.com/login/oauth/authorize';
    public static $tokenURL = 'https://github.com/login/oauth/access_token';
    public static $apiURLBase = 'https://api.github.com/';
    public $clientID;
    public $clientSecret;
    public $redirectURL;
    private static $instance = null;

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    //Get option
    public function setGithubOption(array $config = [])
    {
        $this->clientID = isset($config['client_id']) ? $config['client_id'] : '';
        if (!$this->clientID) {
            echo 'Required "client_id" key not supplied in config';
        }
        $this->clientSecret = isset($config['client_secret']) ? $config['client_secret'] : '';
        if (!$this->clientSecret) {
            echo 'Required "client_secret" key not supplied in config';
        }
        $this->redirectURL = isset($config['redirect_url']) ? $config['redirect_url'] : '';
    }
    
    /**
     * Get the authorize URL
     *
     * @return a string
     */
    public function getAuthorizeURL($state)
    {
        return self::$authorizeURL.'?'.http_build_query([
            'client_id' => $this->clientID,
            'redirect_url' => $this->redirectURL,
            'state' => $state,
            'scope' => 'user:email'
        ]);
    }
    
    //Exchange token and code for an access token
    public function getAccessToken($state, $oauth_code)
    {
        $token = self::apiRequest(self::$tokenURL.'?'.http_build_query([
            'client_id' => $this->clientID,
            'client_secret' => $this->clientSecret,
            'state' => $state,
            'code' => $oauth_code
        ]));
        return $token->access_token;
    }
    
    /**
     * Make an API request
     *
     * @return API results
     */
    public function apiRequest($access_token)
    {
        $apiURL = filter_var($access_token, FILTER_VALIDATE_URL) ? $access_token : self::$apiURLBase.'user?access_token='.$access_token;
        $context = stream_context_create([
          'http' => [
            'user_agent' => 'GitHub OAuth Login',
            'header' => 'Accept: application/json'
          ]
        ]);
        $response = file_get_contents($apiURL, false, $context);
        return $response ? json_decode($response) : $response;
    }
}
