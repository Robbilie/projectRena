<?php
namespace ProjectRena\Model\OAuth;

use ProjectRena\RenaApp;

/**
 * Class EVEOAuth
 *
 * @package ProjectRena\Model\OAuth
 */
class EVEOAuth
{
				/**
				 * @var RenaApp
				 */
				private $app;
				/**
				 * @var \ProjectRena\Lib\Db
				 */
				private $db;
				/**
				 * @var \ProjectRena\Lib\baseConfig
				 */
				private $config;

				/**
				 * @param RenaApp $app
				 */
				function __construct(RenaApp $app)
				{
								$this->app = $app;
								$this->db = $this->app->Db;
								$this->config = $this->app->baseConfig;
				}

				/**
				 * @return string
				 */
				public function LoginURL()
				{
								$requestURI = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : $this->app->request->getPath();

								return "https://login.eveonline.com/oauth/authorize?response_type=code&redirect_uri=" . $this->app->request->getUrl() . $this->config->getConfig("callBack", "crestsso", "/login/eve/") . "&client_id=" . $this->config->getConfig("clientID", "crestsso") . "&scope=publicData" . "&state=" . $requestURI;
				}

				/**
				 * @param $code
				 * @param $state
				 */
				public function SSOLogin($code, $state)
				{
								// Get the login token
								$tokenURL = "https://login.eveonline.com/oauth/token";
								$base64 = base64_encode($this->config->getConfig("clientID", "crestsso") . ":" . $this->config->getConfig("secretKey", "crestsso"));
								$data = json_decode($this->app->cURL->sendData($tokenURL, array(
										"grant_type" => "authorization_code",
										"code"       => $code,
									), array("Authorization: Basic {$base64}")));

								$accessToken = $data->access_token;
								$refreshToken = $data->refresh_token;
								//$expiresIn = $data->expires_in;

								// Verify token
								$verifyURL = "https://login.eveonline.com/oauth/verify";
								$data = json_decode($this->app->cURL->sendData($verifyURL, array(), array("Authorization: Bearer {$accessToken}")));

								$characterID = $data->CharacterID;
								$characterName = $data->CharacterName;
								$characterOwnerHash = $data->CharacterOwnerHash;
								//$expiresOn = $data->ExpiresOn;
								$scopes = $data->Scopes;
								$tokenType = $data->TokenType;

								// Generate a unique id
								$uniqueID = uniqid("", true);

								// Insert it all to the table
								$this->app->Db->execute("INSERT IGNORE INTO users (characterName, characterID, characterOwnerHash, loginHash, accessToken, refreshToken, scopes, tokenType) VALUES (:characterName, :characterID, :characterOwnerHash, :loginHash, :accessToken, :refreshToken, :scopes, :tokenType)", array(
										":characterName" => $characterName,
										":characterID" => $characterID,
										":characterOwnerHash" => $characterOwnerHash,
										":loginHash" => $uniqueID,
										":accessToken" => $accessToken,
										":refreshToken" => $refreshToken,
										":scopes" => $scopes,
										":tokenType" => $tokenType,
									));
        
        // Login with CoreManager
        $this->app->CoreManager->login($characterID);

								// Create the auto login cookie
								$this->app->setEncryptedCookie($this->config->getConfig("name", "cookies", "rena"), $uniqueID, time() + $this->config->getConfig("time", "cookies", (3600 * 24 * 30)), "/", $this->app->request->getHost(), $this->config->getConfig("ssl", "cookies", true), "true");

								// Set the session
								$_SESSION["characterName"] = $characterName;
								$_SESSION["characterID"] = $characterID;
								$_SESSION["loggedin"] = true;

								// Redirect back to where the person came from
								$this->app->redirect($state);
				}
}