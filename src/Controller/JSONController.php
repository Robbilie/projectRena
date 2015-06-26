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

    function getStatusArray () {
        $user = null;
        if(isset($_SESSION['characterID']))
            $user = $this->app->CoreManager->getCharacter($_SESSION['characterID'])->getCUser();
        $status = array(
            "isLoggedin" => isset($_SESSION["loggedin"]) ? $_SESSION["loggedin"] : false,
            "isAdmin" => !is_null($user) && $user->isAdmin() ? true : false,
            "charname" => isset($_SESSION["characterName"]) ? $_SESSION["characterName"] : '',
            "charid" => isset($_SESSION["characterID"]) ? $_SESSION["characterID"] : 0
        );
        return $status;
    }

    public function submitAPIKey ($keyID, $vCode) {
        $rep = array("msg" => "", "state" => "error");
        if(isset($_SESSION['loggedin'])) {
            $xmldata = @file_get_contents("https://api.eveonline.com/account/APIKeyInfo.xml.aspx?keyID={$keyID}&vCode={$vCode}");
            if(!$xmldata) {
                $resp['msg'] = 'The data you entered is invalid.';
            } else {
                $xml = new SimpleXMLElement($xmldata);
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

    public function getCharacter ($characterID) {
        $character = array();
        if(isset($_SESSION['loggedin']))
            $character = $this->app->CoreManager->getCharacter($characterID);
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($character));
    }

    public function getCharacters () {
        $characters = array();
        if(isset($_SESSION['loggedin']))
            $characters = $this->app->CoreManager->getCharacter($_SESSION['characterID'])->getCUser()->getChars();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($characters));
    }

    public function switchCharacter ($characterID) {
        $resp = array("state" => "error");
        if(isset($_SESSION['loggedin'])) {
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

    public function removeCharacter ($characterID) {
        $resp = array("state" => "error");
        if(isset($_SESSION['loggedin'])) {
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

    public function getCharacterGroups ($characterID) {
        $groups = array();
        if(isset($_SESSION['loggedin']))
            $groups = $this->app->CoreManager->getCharacter($characterID)->getCGroups();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($groups));
    }

    // return the controltower visible to the character
    public function getControltowers () {
        $controlTower = array();
        if(isset($_SESSION['loggedin'])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            if($char->hasPermission("viewAllianceControltower")) {
                $controlTower = $char->getCCorporation()->getCAlliance()->getControltower();
            } else if($char->hasPermission("viewCorporationControltower")) {
                $controlTower = $char->getCCorporation()->getControltower();
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($controlTower));
    }

    public function getCorporationContents ($corporationID, $containerID) {
        $resp = array("name" => "", "list" => array());
        if(isset($_SESSION['loggedin'])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $corp = $this->app->CoreManager->getCorporation($corporationID);
            $item = $this->app->CoreManager->getItem($containerID);
            if($item)
                $resp['name'] = $item->getName();
            if(
                ($corp->getId() == $char->getCorpId() && $char->hasPermission("viewCorporationAssets")) ||
                ($corp->getAlliance() == $char->getAlliId() && $char->hasPermission("viewAllianceAssets"))
            ) {
                $resp['list'] = $corp->getItems(function ($i) use ($containerID) { return $i->getLocationId() == $containerID; });
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));

    }

    public function getControltower ($towerID) {
        $resp = array("name" => "", "moonname" => "", "state" => "", "typename" => "", "fuel" => "", "strontium" => "", "modules" => array());
        if(isset($_SESSION['loggedin'])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $tower = $this->app->CoreManager->getControlTower($towerID);
            if(
                ($tower->getOwner()->getCAlliance()->getId() == $char->getAlliId() && $char->hasPermission("viewAllianceControltower")) ||
                ($tower->getOwner()->getId() == $char->getCorpId() && $char->hasPermission("viewCorporationControltower"))
            ) {
                $resp = array("id" => $tower->getId(),"name" => $tower->getName(), "moonname" => $tower->getMoon()->getName(), "state" => $tower->getState(), "typename" => $tower->getType()->getName(), "fuel" => $tower->getFuelLevel(), "strontium" => $tower->getStrontiumLevel(), "modules" => $tower->getModules());
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    public function getGroups () {
        $groups = array("owned" => array(), "corporation" => array(), "alliance" => array(), "groups" => array());
        if(isset($_SESSION['loggedin'])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
			$groups['cancreate'] = $char->hasPermission("createGroup");
            $groups['groups'] = $char->getGroups();
            // corp group if ceo and corp scoped groups
            if($char->getCCorporation()->getCeoCharacterId() == $char->getCharId()) {
                $groups['owned'] = array_merge($groups['owned'], $this->app->CoreManager->getGroupsByOwnerAndScope($char->getCorpId(), "corporation"));
            }
            // alliance group if alli ceo and alliance scoped groups
            if($char->getCCorporation()->getCAlliance()->getExecCorp()->getCeoCharacterId() == $char->getCharId()) {
                $groups['owned'] = array_merge($groups['owned'], $this->app->CoreManager->getGroupsByOwnerAndScope($char->getAlliId(), "alliance"));
            }
            // admin scoped groups if user admin
            if($char->getCUser()->isAdmin()) {
                $groups['owned'] = array_merge($groups['owned'], $this->app->CoreManager->getGroupsByOwnerAndScope(null, "admin"));
            }
            $groups['corporation'] = array_merge($groups['corporation'], $this->app->CoreManager->getGroupsByOwnerAndScope($char->getCorpId(), "corporation"));
            $groups['alliance'] = array_merge($groups['alliance'], $this->app->CoreManager->getGroupsByOwnerAndScope($char->getAlliId(), "alliance"));
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($groups));
    }

    public function getGroup ($groupID) {
        $group = array();
        if(isset($_SESSION['loggedin'])) {
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            $group->getPermissions();
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($group));
    }

    public function getGroupMembers ($groupID) {
        $members = array();
        if(isset($_SESSION['loggedin'])) {
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            $members = $group->getCCharacters();
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($members));
    }

    public function getPermissionsByScope ($scope) {
        $permissions = array();
        if(isset($_SESSION['loggedin']))
            $permissions = $this->app->CoreManager->getPermissionsByScope(str_replace("private", "", $scope));
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($permissions));
    }

    public function removePermissionFromGroup ($groupID, $permissionID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION['loggedin'])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            if($group->hasPermission($permissionID)) {
                if($this->app->CoreManager->charHasGroupPrivs($char, $group)) {
                    $group->removePermission($permissionID);
                    $resp['state'] = "success";
                } else {
                    $resp['msg'] = "You are not permitted to do this.";
                }
            } else {
                $resp['msg'] = "Group does not have Permission.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    public function addPermissionToGroup ($groupID, $permissionID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION['loggedin'])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            $permission = $this->app->CoreManager->getPermission((int)$permissionID);
            if($this->app->CoreManager->charHasGroupPrivs($char, $group)) {
                $group->addPermission($permissionID);
                $resp['state'] = "success";
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    public function removeCharacterFromGroup ($groupID, $characterID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION['loggedin'])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $otherchar = $this->app->CoreManager->getCharacter($characterID);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            if($this->app->CoreManager->charCanAddCharToGroup($char, $otherchar, $group)) {
                if(in_array($group->getId(), $otherchar->getGroups())) {
                    $group->removeCharacter($otherchar->getCharId());
                    $resp['state'] = "success";
                } else {
                    $resp['msg'] = "Character not in Group.";
                }
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    public function addCharacterToGroup ($groupID, $characterID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION['loggedin'])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $otherchar = $this->app->CoreManager->getCharacter($characterID);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            if($this->app->CoreManager->charCanAddCharToGroup($char, $otherchar, $group)) {
                $group->addCharacter($otherchar->getCharId());
                $resp['state'] = "success";
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    public function createGroup ($name, $scope, $private) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION['loggedin'])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $fakegroup = new \ProjectRena\Model\CoreGroup($this->app, array("scope" => $scope, "owner" => $private ? ($scope == "corporation" ? $char->getCorpId() : ($scope == "alliance" ? $char->getAlliId() : null)) : null));
            if($this->app->CoreManager->charHasGroupPrivs($char, $fakegroup)) {
                if(trim($name) != "") {
                    if(!$this->app->CoreManager->entityExists($name)) {
                        $this->app->CoreManager->createGroup($name, $scope, $fakegroup->getOwner(), 1);
                        $resp['state'] = "success";
                    } else {
                        $resp['msg'] = "Name cannot be Corp/Alli Name.";
                    }
                } else {
                    $resp['msg'] = "Name cant be blank.";
                }
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    public function getCorporation ($corporationID) {
        $corp = array();
        if(isset($_SESSION['loggedin'])) {
            $corp = $this->app->CoreManager->getCorporation($corporationID);
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($corp));
    }

    public function getCorporationMembers ($corporationID) {
        $members = array();
        if(isset($_SESSION['loggedin'])) {
            $corp = $this->app->CoreManager->getCorporation($corporationID);
            $members = $corp->getFullMemberList();
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($members));
    }

    public function getAlliance ($allianceID) {
        $alliance = array();
        if(isset($_SESSION['loggedin'])) {
            $alliance = $this->app->CoreManager->getAlliance($allianceID);
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($alliance));
    }

    public function getAllianceMembers ($allianceID) {
        $members = array();
        if(isset($_SESSION['loggedin'])) {
            $alliance = $this->app->CoreManager->getAlliance($allianceID);
            $corporations = $alliance->getCorpList();
            foreach ($corporations as $corporation)
                $members = array_merge($members, $corporation->getFullMemberList());
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($members));
    }

    public function getAllianceCorporations ($allianceID) {
        $corporations = array();
        if(isset($_SESSION['loggedin'])) {
            $alliance = $this->app->CoreManager->getAlliance($allianceID);
            $corporations = $alliance->getCorpList();
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($corporations));
    }

    public function findSystemNames ($name) {
        $systemRows = $this->db->query("SELECT solarSystemName as name, solarSystemID as data FROM mapSolarSystems WHERE solarSystemName LIKE :name", array(":name" => $name."%"));
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($systemRows));
    }

    public function findCharacterNames ($name) {
        $characterRows = $this->db->query("SELECT characterName as name, characterID as data FROM easCharacters WHERE characterName LIKE :name", array(":name" => $name."%"));
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($characterRows));
    }

}
