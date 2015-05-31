<?php

namespace ProjectRena\Controller;

use ProjectRena\Model\OAuth\EVE;
use ProjectRena\RenaApp;

/**
 * Class LoginController
 * @package ProjectRena\Controller
 */
class LoginController
{
    protected $app;
    private $config;

    public function __construct(RenaApp $app)
    {
        $this->app = $app;
        $this->config = $app->baseConfig;
    }

    public function loginEVE()
    {
        // Instantiate the Eve Online service using the credentials, http client, storage mechanism for the token and profile scope
        $SSOInit = new EVE($this->app);
        $eveService = $SSOInit->init();
        if ($eveService->isGlobalRequestArgumentsPassed()) {
            $result = $eveService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/oauth/verify');
            if ($result) {
                $characterID = $result["CharacterID"];
                $characterName = $result["CharacterName"];
                $characterOwnerHash = $result["CharacterOwnerHash"];
                // Insert the user to the table
                $userID = $this->app->users->createUserWithCrest($characterName, $characterID, $characterOwnerHash);
                // Set the session
                //$cookieName = $this->config->getConfig("name", "cookies");
                //$cookieSSL = $this->config->getConfig("ssl", "cookies");
                //$cookieTime = $this->config->getConfig("time", "cookies");
                //$cookieSecret = $this->config->getConfig("secret", "cookies");
                //$hash =
                //$this->app->setEncryptedCookie($cookieName, )
                $_SESSION["loggedin"] = true;
                $this->app->redirect("/");
            }
        } else {
            $eveService->redirectToAuthorizationUri();
        }
    }
}
