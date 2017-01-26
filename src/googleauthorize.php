<?php

include_once __DIR__ . '/../vendor/autoload.php';

/**
 * Class that helps with authorization into google API
 */
class GoogleAuthorize
{
	/**
	 * @var Google_Client $client
	 */
	private $client;


	function __construct($o2AuthCredentials, $redirect_uri, $scope)
	{
		$this->client = new Google_Client();
		$this->client->setAuthConfig($o2AuthCredentials);
		$this->client->setRedirectUri($redirect_uri);
		$this->client->setAccessType('offline');
		$this->client->addScope($scope);
	}

	function getGoogleClient()
	{
		return $this->client;
	}

	/**
	 * Check if access token is set in session
	 * @return boolean 
	 */
	function isLoggedIn()
	{

		if(isset($_SESSION['access_token']['google_drive']))
			return (!$this->client->isAccessTokenExpired($_SESSION['access_token']['google_drive']));
	}


	/**
	 * Creates authorized url which redirects user to page for authentication 
	 * @return string
	 */
	function getAuthorizeUrl()
	{
		return $this->client->createAuthUrl();
	}

	/**
	 * If authorization code is set, authenticate client and call @setToken method
	 */
	function getToken()
	{
		if(isset($_GET['code']))
		{
			$this->client->authenticate($_GET['code']);
			$this->setToken($this->client->getAccessToken());
		} 

	}

	
	/**
	 * Assign token to SESSION and also set it as part of client object
	 * @param string $token JSON string
	 */
	function setToken($token)
	{
		$_SESSION['access_token']['google_drive'] = $token;
		try{
			$this->client->setAccessToken($token);
		}
		catch(InvalidArgumentException $e)
		{
			print ($e->getMessage());	
		}
	}

	
	/**
	 * Returns refresh token which user gets first time he allows access to application
	 * @return string
	 */
	function getRefreshToken()
	{
		if(isset($_SESSION['access_token']['google_drive']['refresh_token']))
		{
			return $_SESSION['access_token']['google_drive']['refresh_token'];
		}
	}

	/**
	 * Uses refresh token to get new access token and sets it as part of client object
	 * @param  string $refreshToken 
	 */
	function getTokenWithRefreshToken($refreshToken = null)
	{
		
		$this->client->refreshToken($refreshToken);
		$this->setToken($this->client->getAccessToken());
	}

	/**
	 * Check if access token is expired and new token is needed to be fetch
	 * @return bool Returns true if token is expired
	 
	function checkExpiration()
	{
		if(isset($_SESSION['access_token']['google_drive']))
			return $this->client->isAccessTokenExpired($_SESSION['access_token']['google_drive']);
	}
*/
}