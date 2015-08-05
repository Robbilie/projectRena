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
            $timeout = 35000000;
            $interval = 500000;
            
            $sessID = session_id();

            $ft = filemtime(session_save_path()."/sess_".$sessID);

            $status = $this->getStatusArray();

            if(md5(json_encode($status)) == $_GET['hash']) {

                session_write_close();

                while($timeout > 0) {
                    clearstatcache();
                    if(filemtime(session_save_path()."/sess_".$sessID) > $ft) {
                        session_start();
                        session_decode(file_get_contents(session_save_path()."/sess_".$sessID));
                        $status = $this->getStatusArray();
                        break;
                    } else {
                        usleep($interval);
                    }

                    $timeout -= $interval;

                }
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

    // return the controltower visible to the character
    public function getControltowers () {
        $controlTower = array();
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            if($char->hasPermission("readControltower", "alliance") && $char->getAlliId() != 0) {
                $controlTower = $char->getCCorporation()->getCAlliance()->getControltower();
            } else if($char->hasPermission("readControltower", "corporation")) {
                $controlTower = $char->getCCorporation()->getControltower();
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($controlTower));
    }

    // get content of a container owned by a corporation
    public function getCorporationContents ($corporationID, $locationID) {
        $resp = array("name" => "", "list" => array());
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $corp = $this->app->CoreManager->getCorporation($corporationID);
            $item = $this->app->CoreManager->getItem($locationID);
            if(!is_null($item)) {
                $resp['name'] = $item->getName();
            } else {
                $location = $this->app->CoreManager->getLocation($locationID, true);
                if($location)
                    $resp['name'] = $location->getName();
            }
            if(
                ($corp->getId() == $char->getCorpId() && $char->hasPermission("readAssets", "corporation")) ||
                ($char->getAlliId() != 0 && $corp->getAlliance() == $char->getAlliId() && $char->hasPermission("readAssets", "alliance"))
            ) {
                $resp['list'] = $corp->getItems(function ($i) use ($locationID) { return $i->getLocationId() == $locationID; });
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));

    }

    // return a controltower
    public function getControltower ($towerID) {
        $towerresp = null;
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $tower = $this->app->CoreManager->getControlTower($towerID);
            if(
                ($char->getAlliId() != 0 && $tower->getOwner()->getCAlliance()->getId() == $char->getAlliId() && $char->hasPermission("readControltower", "alliance")) ||
                ($tower->getOwner()->getId() == $char->getCorpId() && $char->hasPermission("readControltower", "corporation"))
            ) {
                $towerresp = $tower;
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($towerresp));
    }

    public function setReactionConnection ($towerID, $source, $destination) {
        $resp = array("state" => "error", "msg" => "");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $tower = $this->app->CoreManager->getControlTower($towerID);
            if($tower->getOwnerId() == $char->getCorpId()) {
                if($char->hasPermission("writeReactionsControltower", "corporation")) {
                    // destination may not be child of source
                    $isChild = false;
                    $st = $destination;
                    while(!$isChild) {
                        $row = $this->db->queryRow("SELECT * FROM easControltowerReactions WHERE source = :source", array(":source" => $st));
                        if(!$row)
                            break;
                        if((int)$row['destination'] == $source)
                            $isChild = true;
                        $st = $row['destination'];
                    }
                    if(($source != $destination) && !$isChild) {
                        $this->db->execute("DELETE FROM easControltowerReactions WHERE towerID = :towerID AND source = :destination AND destination = :source", array(":towerID" => $towerID, ":source" => $source, ":destination" => $destination));
                        $this->db->execute("INSERT INTO easControltowerReactions (towerID, source, destination) VALUES (:towerID, :source, :destination) ON DUPLICATE KEY UPDATE towerID = :towerID , source = :source , destination = :destination", array(":towerID" => $towerID, ":source" => $source, ":destination" => $destination));
                        $resp['state'] = "success";
                    }
                } else {
                    $resp['msg'] = "You are not permitted to do this.";
                }
            } else {
                $resp['msg'] = "Your corp does not own the tower";
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

            if($char->hasPermission("readJournal", "corporation")) {
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
            }

            $taxes['globalstr'] = number_format($taxes["global"], 2, ',', '.');

        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($taxes));
    }

    public function getAllianceRattingTax ($from = null, $till = null) {
        $totaltaxes = array();
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);

            $corpIDs = [];
            if($char->hasPermission("readJournal", "alliance")) {
                $corporations = $char->getCCorporation()->getCAlliance()->getCorpList();
                foreach ($corporations as $corp)
                    $corpIDs[] = $corp->getId();
            } else if($char->hasPermission("readJournal", "corporation")) {
                $corpIDs[] = $char->getCorpId();
            }
            $corps = $this->app->CoreManager->getCorporations($corpIDs);
            foreach ($corps as $corp) {
                $taxes = array("name" => $corp->getName(), "id" => $corp->getId(), "entries" => array(), "global" => 0, "globalstr" => "");
                $journalRows = $this->db->query("SELECT * FROM ntJournal WHERE ownerID = :ownerID AND accountKey = 1000 AND date > :from AND date < :till AND refTypeID IN (17,33,34,85,99) ORDER BY date DESC",
                    array(
                        ":ownerID"=> $corp->getId(),
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
                $totaltaxes[] = $taxes;
            }

        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($totaltaxes));
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
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $corp = $this->app->CoreManager->getCorporation($corporationID);
            if($corp)
                $members = $corp->getFullMemberList(null, $corp->getId() == $char->getCorpId() && $char->hasPermission("readCoverageAPI", "corporation"));
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
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $alliance = $this->app->CoreManager->getAlliance($allianceID);
            if($alliance) {
                $corporations = $alliance->getCorpList();
                foreach ($corporations as $corporation)
                    $members = array_merge($members, $corporation->getFullMemberList(null, $alliance->getId() == $char->getAlliId() && $char->hasPermission("readCoverageAPI", "alliance")));
            }
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
