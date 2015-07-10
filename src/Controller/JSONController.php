<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class JSONController
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

    // longpolled status, normal status if no hash specified
    public function getStatus () {
        $status = array();

        if(isset($_GET['hash']) && $_GET['hash'] != "") {
            $timeout = 15000000;
            $interval = 500000;
            while($timeout > 0) {

                session_start();
                $status = $this->getStatusArray();
                session_write_close();

                if(md5(json_encode($status)) == $_GET['hash']) {
                    usleep($interval);
                } else {
                    break;
                }

                $timeout -= $interval;

            }
        } else {
            $status = $this->getStatusArray();
            $status['newhash'] = md5(json_encode($status));
        }

        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($status));
    }

    // generate the status array
    function getStatusArray () {
        $user = null;
        if(isset($_SESSION['characterID']))
            $user = $this->app->CoreManager->getCharacter($_SESSION['characterID'])->getCUser();
        $status = array(
            "isLoggedin" => isset($_SESSION["loggedIn"]) ? $_SESSION["loggedIn"] : false,
            "isAdmin" => !is_null($user) && $user->isAdmin() ? true : false,
            "charname" => isset($_SESSION["characterName"]) ? $_SESSION["characterName"] : '',
            "charid" => isset($_SESSION["characterID"]) ? $_SESSION["characterID"] : 0
        );
        return $status;
    }

    // save api key to the db after check
    public function submitAPIKey ($keyID, $vCode) {
        $rep = array("msg" => "", "state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $xmldata = @file_get_contents("https://api.eveonline.com/account/APIKeyInfo.xml.aspx?keyID={$keyID}&vCode={$vCode}");
            if(!$xmldata) {
                $resp['msg'] = 'The data you entered is invalid.';
            } else {
                $xml = simplexml_load_string($xmldata);
                if($xml->result->key['expires'] != "") {
                    $resp['msg'] = 'Please use a Key that doesnt expire.';
                } else {
                    $this->db->execute("INSERT IGNORE INTO ntAPIKey (keyID, vCode) VALUES (:keyID, :vCode)", array(":keyID" => $keyID, ":vCode" => $vCode));
                    $resp['state'] = "success";
                    $resp['msg'] = "Successfully submitted API Key, the data will be fetched in a few minutes.";
                }
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    // return a character
    public function getCharacter ($characterID) {
        $character = array();
        if(isset($_SESSION["loggedIn"]))
            $character = $this->app->CoreManager->getCharacter($characterID);
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($character));
    }

    // return characters associated to user
    public function getCharacters () {
        $characters = array();
        if(isset($_SESSION["loggedIn"]))
            $characters = $this->app->CoreManager->getCharacter($_SESSION['characterID'])->getCUser()->getChars();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($characters));
    }

    // switch to a different character on user
    public function switchCharacter ($characterID) {
        $resp = array("state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($characterID);
            if($char->getCUser()->getId() == $this->app->CoreManager->getCharacter($_SESSION['characterID'])->getCUser()->getId()) {
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

    // return the controltower visible to the character
    public function getControltowers () {
        $controlTower = array();
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            if($char->hasPermission("readControltower", "alliance")) {
                $controlTower = $char->getCCorporation()->getCAlliance()->getControltower();
            } else if($char->hasPermission("readControltower", "corporation")) {
                $controlTower = $char->getCCorporation()->getControltower();
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($controlTower));
    }

    // get content of a container owned by a corporation
    public function getCorporationContents ($corporationID, $containerID) {
        $resp = array("name" => "", "list" => array());
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $corp = $this->app->CoreManager->getCorporation($corporationID);
            $item = $this->app->CoreManager->getItem($containerID);
            if($item)
                $resp['name'] = $item->getName();
            if(
                ($corp->getId() == $char->getCorpId() && $char->hasPermission("readAssets", "corporation")) ||
                ($corp->getAlliance() == $char->getAlliId() && $char->hasPermission("readAssets", "alliance"))
            ) {
                $resp['list'] = $corp->getItems(function ($i) use ($containerID) { return $i->getLocationId() == $containerID; });
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));

    }

    // return a controltower
    public function getControltower ($towerID) {
        $resp = array("name" => "", "moonname" => "", "state" => "", "typename" => "", "fuel" => "", "strontium" => "", "modules" => array());
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $tower = $this->app->CoreManager->getControlTower($towerID);
            if(
                ($tower->getOwner()->getCAlliance()->getId() == $char->getAlliId() && $char->hasPermission("readControltower", "alliance")) ||
                ($tower->getOwner()->getId() == $char->getCorpId() && $char->hasPermission("readControltower", "corporation"))
            ) {
                $resp = array("id" => $tower->getId(),"name" => $tower->getName(), "moonname" => $tower->getMoon()->getName(), "state" => $tower->getState(), "typename" => $tower->getType()->getName(), "fuel" => $tower->getFuelLevel(), "strontium" => $tower->getStrontiumLevel(), "modules" => $tower->getModules());
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    // get basic ratting tax of current corp
    public function getCorporationRattingTax ($from = null, $till = null) {
      $taxes = array("entries" => array(), "global" => 0, "globalstr" => "");
      if(isset($_SESSION["loggedIn"])) {
          $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);

          $journalRows = $this->db->query("SELECT * FROM ntJournal WHERE ownerID = :ownerID AND accountKey = 1000 AND date > :from AND date < :till AND refTypeID IN (17,33,34,85,99) ORDER BY date DESC",
            array(
              ":ownerID"=> $char->getCorpId(),
              ":from"=> !is_null($from) ? strtotime($from) : mktime(0, 0, 0, date("m"), 1, date("Y")),
              ":till"=> !is_null($till) ? strtotime($till) : time()
            )
          );
          $tmpdata = array();
          $tmpuser = array();
          foreach ($journalRows as $journalRow) {
            if(!isset($tmpdata[$journalRow['ownerID2']])) $tmpdata[$journalRow['ownerID2']] = 0;
            $tmpdata[$journalRow['ownerID2']] += $journalRow['amount'];
            $taxes['global'] += $journalRow['amount'];
            $tmpuser[$journalRow['ownerID2']] = $journalRow['ownerName2'];
          }
          arsort($tmpdata);
          foreach($tmpdata as $key => $value) {
            array_push($taxes['entries'], array("ownerID" => $key, "ownerName" => $tmpuser[$key], "valuestr" => number_format($tmpdata[$key], 2, ',', '.'), "value" => $tmpdata[$key]));
          }

          $taxes['globalstr'] = number_format($taxes["global"], 2, ',', '.');

      }
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($taxes));
    }

    // return all permission with a special scope
    public function getPermissionsByScope ($scope) {
        $permissions = array();
        if(isset($_SESSION["loggedIn"]))
            $permissions = $this->app->CoreManager->getPermissionsByScope(str_replace("private", "", $scope));
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($permissions));
    }

    // return a special corporation
    public function getCorporation ($corporationID) {
        $corp = array();
        if(isset($_SESSION["loggedIn"])) {
            $corp = $this->app->CoreManager->getCorporation($corporationID);
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($corp));
    }

    // return the members of a corporation
    public function getCorporationMembers ($corporationID) {
        $members = array();
        if(isset($_SESSION["loggedIn"])) {
            $corp = $this->app->CoreManager->getCorporation($corporationID);
            $members = $corp->getFullMemberList();
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($members));
    }

    // return a  special alliance
    public function getAlliance ($allianceID) {
        $alliance = array();
        if(isset($_SESSION["loggedIn"])) {
            $alliance = $this->app->CoreManager->getAlliance($allianceID);
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($alliance));
    }

    // return the members of an alliance
    public function getAllianceMembers ($allianceID) {
        $members = array();
        if(isset($_SESSION["loggedIn"])) {
            $alliance = $this->app->CoreManager->getAlliance($allianceID);
            $corporations = $alliance->getCorpList();
            foreach ($corporations as $corporation)
                $members = array_merge($members, $corporation->getFullMemberList());
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($members));
    }

    // return the corporations in an alliance
    public function getAllianceCorporations ($allianceID) {
        $corporations = array();
        if(isset($_SESSION["loggedIn"])) {
            $alliance = $this->app->CoreManager->getAlliance($allianceID);
            $corporations = $alliance->getCorpList();
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($corporations));
    }

}
