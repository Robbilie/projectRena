<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class CharactersController
{
    /**
     * @var RenaApp
     */
    protected $app;
    protected $db;
    protected $config;

    /**
     * @param RenaApp $app
     */
    public function __construct(RenaApp $app)
    {
        $this->app = $app;
        $this->db = $this->app->Db;
        $this->config = $this->app->baseConfig;
    }

    // return characters associated to user
    public function getCharacters () {
        $characters = array();
        if(isset($_SESSION["loggedIn"]))
            $characters = $this->app->CoreManager->getCharacter($_SESSION['characterID'])->getCUser()->getChars();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($characters));
    }

    // return a character
    public function getCharacter ($characterID) {
        $character = array();
        if(isset($_SESSION["loggedIn"]))
            $character = $this->app->CoreManager->getCharacter($characterID);
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($character));
    }

    // switch to a different character on user
    public function switchCharacter ($characterID) {
        $resp = array("state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($characterID);
            if($char->getCUser()->isAdmin() || $char->getCUser()->getId() == $this->app->CoreManager->getCharacter($_SESSION['characterID'])->getCUser()->getId()) {
                $this->app->CoreManager->createCharacter($char->getCharId());
                $_SESSION["characterName"] = $char->getCharName();
                $_SESSION["characterID"] = $char->getCharId();
                $resp['state'] = "success";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    // remove character bound to user
    public function removeCharacter ($characterID) {
        $resp = array("state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($characterID);
            $inChar = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            if($char->getUser() && $char->getCUser()->getId() == $inChar->getCUser()->getId() && $char->getId() != $inChar->getId()) {
              $char->setUser(null);
              $resp['state'] = "success";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    // get the groups a character is in
    public function getCharacterGroups ($characterID) {
        $groups = array();
        if(isset($_SESSION["loggedIn"]))
            $groups = $this->app->CoreManager->getCharacter($characterID)->getCGroups();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($groups));
    }

    public function getCharacterOptions ($characterID) {
        $options = array();
        if(isset($_SESSION["loggedIn"])) {
            $tmpoptions = $this->app->CoreManager->getCharacter($characterID)->getOptions();
            foreach ($tmpoptions as $option) {
                if(!isset($options[$option['key']])) $options[$option['key']] = array();
                array_push($options[$option['key']], $option['value']);
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($options));
    }

    public function setCharacterOption ($characterID, $key, $value) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($characterID);
            switch ($key) {
                case 'jpw':
                    $jname = $char->getStripCharName();
                    $reps['msg'] = file_get_contents("https://www.nemesisenterprises.de/auth/external.php?token=".$this->config->getConfig("jabberreg", "secrets")."&user=".$jname."&password=".$value);
                    $resp['state'] = "success";
                    break;

                default:
                    break;
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

}
