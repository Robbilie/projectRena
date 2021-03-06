<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;
use ProjectRena\Model\Core\CoreCharacter;

class IntelController
{
    /**
     * @var RenaApp
     */
    protected $app;
    protected $db;
    protected $config;

    protected $maxIntelAge = 0;

    /**
     * @param RenaApp $app
     */
    public function __construct(RenaApp $app)
    {
        $this->app = $app;
        $this->db = $this->app->Db;
        $this->config = $this->app->baseConfig;

        $this->maxIntelAge = time() - (60 * 60 * 2);
    }

    public function getSystemIntel ($psystemID = null) {
        $systemID = $psystemID;
        $intel = array(
                "state" => 0,
                "status" => "Offline",
                "systemID" => 0,
                "systemName" => "",
                "regionID" => 0,
                "regionName" => 0,
                "neighbours" => array(),
                "members" => array()
            );
        if(isset($_SESSION["loggedIn"])) {

            // set system id
            if(is_null($systemID) && isset($_SERVER['HTTP_EVE_TRUSTED']) && $_SERVER['HTTP_EVE_TRUSTED'] == "Yes")
                $systemID = (int)$_SERVER['HTTP_EVE_SOLARSYSTEMID'];
            if(is_null($systemID))
                $systemID = $this->app->CoreManager->getCharacterLocation($_SESSION['characterID']);
            if(is_null($systemID) || $systemID == 0)
                $systemID = 30002489;

            if(isset($_GET['hash']) && $_GET['hash'] != "") {
                $timeout    = 15000000;
                $interval   =  1000000;
            
                $sessID = session_id();
                $charid = $_SESSION['characterID'];
                session_write_close();
                $ft = md5_file(session_save_path()."/sess_".$sessID);
                while($timeout > 0) {

                    clearstatcache();
                    if(md5_file(session_save_path()."/sess_".$sessID) != $ft) break;

                    $begintime = time()+microtime();

                    $intel = $this->getSystemIntelArray($systemID, $charid);

                    $sysids = array();
                    array_push($sysids, $systemID);

                    $systemsDone = $this->db->query("SELECT fromSolarSystemID as id FROM mapSolarSystemJumps WHERE toSolarSystemID = :systemID", array(":systemID" => $systemID));
                    foreach($systemsDone as $systemDone) {
                        if(!in_array((int)$systemDone['id'], $sysids)) {
                            $oneIntel = $this->getSystemIntelArray((int)$systemDone['id'], $charid);
                            $oneIntel['distance'] = 1;

                            if($intel['state'] < 4 && $oneIntel['hostilecount'] > 0) {
                                $intel['state'] = 3;
                                $intel['status'] = "Attention...";
                            }

                            array_push($intel['neighbours'], $oneIntel);
                            array_push($sysids, (int)$systemDone['id']);

                            $systemsDtwo = $this->db->query("SELECT fromSolarSystemID as id FROM mapSolarSystemJumps WHERE toSolarSystemID = :systemID", array(":systemID" => (int)$systemDone['id']));
                            foreach($systemsDtwo as $systemDtwo) {
                                if(!in_array((int)$systemDtwo['id'], $sysids)) {
                                    $twoIntel = $this->getSystemIntelArray((int)$systemDtwo['id'], $charid);
                                    $twoIntel['distance'] = 2;

                                    if($intel['state'] < 3 && $twoIntel['hostilecount'] > 0) {
                                        $intel['state'] = 2;
                                        $intel['status'] = "Wake Up...";
                                    }

                                    array_push($intel['neighbours'], $twoIntel);
                                    array_push($sysids, (int)$systemDtwo['id']);
                                }
                            }
                        }
                    }

                    if($intel['hostilecount'] > 0) {
                        $intel['state'] = 4;
                        $intel['status'] = "Warning";
                    }

                    $endtime = time()+microtime();

                    //$intel['calctime'] = ($endtime - $begintime);

                    if(md5(json_encode($intel)) == $_GET['hash']) {
                        usleep($interval);
                    } else {
                        break;
                    }

                    $timeout -= $interval;

                }
            } else {
                $intel = $this->getSystemIntelArray($psystemID, $_SESSION['characterID']);
                $intel['newhash'] = md5(json_encode($intel));
            }

        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($intel));
    }

    function getSystemIntelArray ($psystemID = null, $characterID) {
        $systemID = $psystemID;

        $intel = array(
            "state" => 0,
            "status" => "Offline",
            "systemID" => 0,
            "systemName" => "",
            "regionID" => 0,
            "regionName" => 0,
            "neighbours" => array(),
            "members" => array()
        );

        // set system id
        if(is_null($systemID) && isset($_SERVER['HTTP_EVE_TRUSTED']) && $_SERVER['HTTP_EVE_TRUSTED'] == "Yes")
            $systemID = (int)$_SERVER['HTTP_EVE_SOLARSYSTEMID'];
        if(is_null($systemID))
            $systemID = $this->app->CoreManager->getCharacterLocation($characterID);
        if(is_null($systemID) || $systemID == 0)
            $systemID = 30002489;

        $char = $this->app->CoreManager->getCharacter($characterID);

        do {

            if(!$char->hasPermission("readIntel", "alliance") && !$char->hasPermission("readIntel", "corporation")) break;

            // move character if not in system yet
            if(isset($_SERVER['HTTP_EVE_TRUSTED']) && $_SERVER['HTTP_EVE_TRUSTED'] == "Yes") {
                if((int)$_SERVER['HTTP_EVE_SOLARSYSTEMID'] != $this->app->CoreManager->getCharacterLocation($characterID))
                    $this->db->execute(
                        "INSERT INTO easTracker
                            (locationID, submitterID, characterID, characterName, corporationID, allianceID, timestamp)
                        VALUES
                            (:locationID, :submitterID, :characterID, :characterName, :corporationID, :allianceID, :ts)",
                        array(
                            ":locationID" => (int)$_SERVER['HTTP_EVE_SOLARSYSTEMID'],
                            ":submitterID" => $characterID,
                            ":characterID" => $characterID,
                            ":characterName" => $char->getName(),
                            ":corporationID" => $char->getCorpId(),
                            ":allianceID" => $char->getAlliId(),
                            ":ts" => time()
                        )
                    );
            }
            // initial values
            $solarSystem = $this->app->mapSolarSystems->getAllByID($systemID);
            $region = $this->app->mapRegions->getAllByID($solarSystem['regionID']);

            $intel['state'] = 1;
            $intel['status'] = "Online";
            $intel['systemID'] = $solarSystem['solarSystemID'];
            $intel['systemName'] = $solarSystem['solarSystemName'];
            $intel['regionID'] = $solarSystem['regionID'];
            $intel['regionName'] = $region['regionName'];
            $intel['hostilecount'] = 0;

            // get members
            $members = $this->db->query(
                "SELECT characterID as id,characterName as name,corporationID,allianceID,submitterID,timestamp,
                    (SELECT info
                    FROM easTrackerInfo
                    WHERE easTrackerInfo.characterID = easTracker.characterID AND timestamp > :ts ORDER BY timestamp DESC LIMIT 0,1
                    ) as info
                FROM easTracker
                WHERE
                    easTracker.locationID = :locationID AND easTracker.timestamp > :ts AND
                    easTracker.timestamp =
                        (SELECT timestamp FROM easTracker as t WHERE
                            t.characterID = easTracker.characterID ORDER BY t.timestamp DESC LIMIT 1) AND easTracker.timestamp > :ts ORDER BY easTracker.characterName ASC",// LIMIT 100",
                array(
                    ":locationID" => $systemID,
                    ":ts" => $this->maxIntelAge
                )
            );
            /*$members = $this->db->query(
                "SELECT easTracker.characterID as id,easTracker.characterName as name,easTracker.corporationID,easTracker.allianceID,easTracker.submitterID,easTracker.timestamp,
                    (SELECT info
                    FROM easTrackerInfo
                    WHERE easTrackerInfo.characterID = easTracker.characterID AND timestamp > :ts ORDER BY timestamp DESC LIMIT 0,1
                    ) as info FROM easTracker INNER JOIN (SELECT max(timestamp) as maxts,characterID FROM easTracker GROUP BY characterID) t ON easTracker.characterID = t.characterID AND easTracker.timestamp = t.maxts WHERE easTracker.timestamp > :ts AND easTracker.locationID = :locationID",
                array(
                    ":locationID" => $systemID,
                    ":ts" => $this->maxIntelAge
                )
            );*/

            if(count($members) <= 100) {

                $intel['membertype'] = "characters";

                $newmembers = array();


                foreach ($members as $member) {

                    $submitter = $this->app->CoreManager->getCharacter($member['submitterID']);
                    if(
                        $char->getId() != $submitter->getId() && 
                        $char->getCorpId() != $submitter->getCorpId() && 
                        ($char->getAlliId() == 0 || $char->getAlliId() != $submitter->getAlliId()) && 
                        $submitter->derivedStanding($char) <= 0 && 
                        !$char->hasPermission("bjhjhlajkhlajksdhflkjasdhflFuckingOpsec")
                    ) continue;

                    $standing = $char->derivedStanding(new CoreCharacter($this->app, array("characterID" => $member['id'], "corporationID" => $member['corporationID'], "allianceID" => $member['allianceID'])));

                    if(($char->getAlliId() != 0 && $char->getAlliId() == $member['allianceID']) || !$char->getCCorporation()->isNPC() && $char->getCorpId() == $member['corporationID'])
                        $standing = 20;

                    if($standing <= 0 || $char->hasPermission("bjhjhlajkhlajksdhflkjasdhflFuckingOpsec")) {
                        $newmembers[$member['id']] = array(
                            "type"      => "character",
                            "id"        => $member['id'],
                            "name"      => $this->app->CoreManager->getCharacter($member['id'])->getName(),
                            "standing"  => $standing,
                            "timestamp" => $member['timestamp'],
                            "info"      => $member['info']
                        );
                        if($standing <= 0)
                            $intel['hostilecount']++;
                    }
                }

                usort($newmembers, function ($a, $b) { return strnatcasecmp($a['name'], $b['name']); });

                $members = $newmembers;
            } else {

                $intel['membertype'] = "noncharacters";

                $noncharacters = array();

                foreach ($members as $member) {

                    $submitter = $this->app->CoreManager->getCharacter($member['submitterID']);
                    if(
                        $char->getId() != $submitter->getId() && 
                        $char->getCorpId() != $submitter->getCorpId() && 
                        ($char->getAlliId() == 0 || $char->getAlliId() != $submitter->getAlliId()) && 
                        $submitter->derivedStanding($char) <= 0 && 
                        !$char->hasPermission("bjhjhlajkhlajksdhflkjasdhflFuckingOpsec")
                    ) continue;
                    
                    $standing = $char->derivedStanding(new CoreCharacter($this->app, array("characterID" => $member['id'], "corporationID" => $member['corporationID'], "allianceID" => $member['allianceID'])));

                    if(($char->getAlliId() != 0 && $char->getAlliId() == $member['allianceID']) || !$char->getCCorporation()->isNPC() && $char->getCorpId() == $member['corporationID'])
                        $standing = 20;

                    if($standing <= 0 || $char->hasPermission("bjhjhlajkhlajksdhflkjasdhflFuckingOpsec")) {
                        if($member['allianceID'] != 0) {
                            if(!isset($noncharacters[$member['allianceID']]))
                                $noncharacters[$member['allianceID']] = array(
                                    "type"      => "alliance",
                                    "id"        => $member['allianceID'],
                                    "name"      => $this->app->CoreManager->getAlliance($member['allianceID'])->getName(),
                                    "count"     => 0,
                                    "standing"  => $standing
                                );
                            $noncharacters[$member['allianceID']]['count']++;
                        } else {
                            if(!isset($noncharacters[$member['corporationID']]))
                                $noncharacters[$member['corporationID']] = array(
                                    "type"      => "corporation",
                                    "id"        => $member['corporationID'],
                                    "name"      => $this->app->CoreManager->getCorporation($member['corporationID'])->getName(),
                                    "count"     => 0,
                                    "standing"  => $standing
                                );
                            $noncharacters[$member['corporationID']]['count']++;
                        }
                    }
                }


                usort($noncharacters, function ($a, $b) { return strnatcasecmp($a['name'], $b['name']); });

                array_map(function ($a) use (&$intel) { if($a['standing'] <= 0) $intel['hostilecount'] += $a['count']; }, $noncharacters);

                $members = $noncharacters;

            }

            $intel['members'] = $members;


        } while (0);

        return $intel;
    }

    public function setSystemIntel ($psystemID = null) {
        $response = array("state" => "error", "msg" => "");

        // intel token auth
        if(isset($_POST['authToken']) && $_POST['authToken'] != "" && isset($_POST['characterID']) && $_POST['characterID'] != "") {
            $user = $this->app->CoreManager->getUserByToken($_POST['authToken']);
            $char = $this->app->CoreManager->getCharacter($_POST['characterID']);
            if($user && $char) {
                if($char->getUser() == $user->getId()) {
                    $_SESSION["loggedIn"] = true;
                    $_SESSION['characterID'] = $char->getId();
                    $_SESSION['characterName'] = $char->getName();
                } else {
                    $response['msg'] = "char not on user";
                }
            } else {
                $response['msg'] = "user or char not there";
            }
        } else {
            $response['msg'] = "token or charid not set";
        }

        $systemID = $psystemID;
        if(isset($_SESSION["loggedIn"])) {

            do {

                $begintime = time()+microtime();

                $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);

                if(!$character->hasPermission("writeIntel", "alliance") && !$character->hasPermission("writeIntel", "corporation")) break;

                // set system id
                if(is_null($systemID) && isset($_SERVER['HTTP_EVE_TRUSTED']) && $_SERVER['HTTP_EVE_TRUSTED'] == "Yes")
                    $systemID = (int)$_SERVER['HTTP_EVE_SOLARSYSTEMID'];
                if(is_null($systemID))
                    $systemID = $this->app->CoreManager->getCharacterLocation($_SESSION['characterID']);
                if(is_null($systemID) || $systemID == 0)
                    $systemID = 30002489;

                $this->app->CoreManager->createLog("intel", array("characterID" => $character->getId(), "systemID" => $systemID));

                // local members
                $local = str_replace("%20", " ", $this->app->request->post('local'));
                while(strpos($local, ",,") !== FALSE)
                    $local = str_replace(",,", ",", $local);
                if($local[0] == ",")
                    $local = substr($local, 1);
                if($local[strlen($local) - 1] == ",")
                    $local = substr($local, 0, strlen($local) - 1);

                if($local == "") {
                    $response['msg'] = "Empty Local";
                    $response['state'] = "error";
                    break;
                }

                $local = explode(",", $local);

                $charid = $_SESSION['characterID'];
                session_write_close();

                echo (time() + microtime() - $begintime)." init<br>"; // 0


                // get ids from api
                $chunkedLocal = array_chunk($local, 250);
                $idsFromAPI = array();
                for($i = 0; $i < count($chunkedLocal); $i++) {
                    $idsFromAPI = array_merge($idsFromAPI, $this->app->EVEEVECharacterID->getData($chunkedLocal[$i])['result']['characters']);
                }

                $newcharids = array_map(function ($a) { return $a['characterID']; }, $idsFromAPI);

                echo (time() + microtime() - $begintime)." got ids<br>"; // 1

                $charactersFromAPI = $this->app->CoreManager->getCharacters($newcharids);

                echo (time() + microtime() - $begintime)." get characters<br>"; // 2

                // get chars in system
                /*$charRows = $this->db->query(
                    "SELECT characterID FROM easTracker WHERE
                        easTracker.locationID = :locationID AND
                        easTracker.timestamp =
                            (SELECT timestamp FROM easTracker as t WHERE
                                t.characterID = easTracker.characterID ORDER BY t.timestamp DESC LIMIT 1)",
                    array(":locationID" => $systemID)
                );*/
                $charRows = $this->db->query(
                    "SELECT * FROM easTracker INNER JOIN (SELECT max(timestamp) as maxts,characterID FROM easTracker GROUP BY characterID) t ON easTracker.characterID = t.characterID AND easTracker.timestamp = t.maxts WHERE easTracker.locationID = :locationID",
                    array(":locationID" => $systemID)
                );

                $systemcharids = array_map(function ($a) { return $a['characterID']; }, $charRows);

                echo (time() + microtime() - $begintime)." get system rows and map to ids<br>"; // 3

                $systemChars = $this->app->CoreManager->getCharacters($systemcharids);

                echo (time() + microtime() - $begintime)." get system core chars<br>"; // 4

                $ts = time();

                // get those not in system anymore
                $dif = array_diff($systemcharids, $newcharids);

                $dif = array_map(
                    function ($d) use ($systemChars, $charid, $ts) {
                        return $systemChars[$d]->getId() != 0 ? array(
                                ":locationID"       => "null",
                                ":submitterID"      => $charid,
                                ":characterID"      => $systemChars[$d]->getId(),
                                ":characterName"    => $systemChars[$d]->getName(),
                                ":corporationID"    => $systemChars[$d]->getCorpId(),
                                ":allianceID"       => $systemChars[$d]->getAlliId(),
                                ":ts"               => $ts
                            ) : null;
                    }, $dif);
                $this->db->multiInsert("INSERT INTO easTracker (locationID, submitterID, characterID, characterName, corporationID, allianceID, timestamp)", $dif);

                echo (time() + microtime() - $begintime)." insert moved<br>"; // 5

                $newids = array_map(
                    function ($id) use ($systemID, $charid, $charactersFromAPI, $ts) {
                        return $charactersFromAPI[$id]->getId() != 0 ? array(
                                ":locationID"       => $systemID,
                                ":submitterID"      => $charid,
                                ":characterID"      => $charactersFromAPI[$id]->getId(),
                                ":characterName"    => $charactersFromAPI[$id]->getName(),
                                ":corporationID"    => $charactersFromAPI[$id]->getCorpId(),
                                ":allianceID"       => $charactersFromAPI[$id]->getAlliId(),
                                ":ts"               => $ts
                            ) : null;
                    }, $newcharids);
                $this->db->multiInsert("INSERT INTO easTracker (locationID, submitterID, characterID, characterName, corporationID, allianceID, timestamp)", $newids);

                echo (time() + microtime() - $begintime)." insert new<br>"; // 6

                $response = array("state" => "success", "msg" => "");

            } while(0);

        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($response));
    }

    public function getRegionIntel ($pregionID = null) {
        $regionID = $pregionID;
        $intel = array();
        if(isset($_SESSION["loggedIn"])) {

            // set system id
            if(is_null($regionID) && isset($_SERVER['HTTP_EVE_TRUSTED']) && $_SERVER['HTTP_EVE_TRUSTED'] == "Yes")
                $regionID = (int)$_SERVER['HTTP_EVE_REGIONID'];
            if(is_null($regionID))
                $regionID = $this->app->mapSolarSystems->getAllByID($this->app->CoreManager->getCharacterLocation($characterID))['regionID'];
            if(is_null($regionID) || $regionID == 0)
                $regionID = 10000029;

            if(isset($_GET['hash']) && $_GET['hash'] != "") {
                $timeout    = 10000000;
                $interval   =   500000;

                $sessID = session_id();
                $charid = $_SESSION['characterID'];
                session_write_close();
                $ft = md5_file(session_save_path()."/sess_".$sessID);
                while($timeout > 0) {

                    clearstatcache();
                    if(md5_file(session_save_path()."/sess_".$sessID) != $ft) break;

                    $begintime = time()+microtime();

                    $intel = $this->getRegionIntelArray($regionID, $charid);

                    $endtime = time()+microtime();

                    //$intel['calctime'] = ($endtime - $begintime);

                    if(md5(json_encode($intel)) == $_GET['hash']) {
                        usleep($interval);
                    } else {
                        break;
                    }

                    $timeout -= $interval;

                }
            } else {
                $intel = $this->getRegionIntelArray($regionID, $_SESSION['characterID']);
                $intel['newhash'] = md5(json_encode($intel));
            }

        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($intel));
    }

    function getRegionIntelArray ($pregionID = null, $characterID) {
        $regionID = $pregionID;

        $intel = array();

        // set system id
        if(is_null($regionID) && isset($_SERVER['HTTP_EVE_TRUSTED']) && $_SERVER['HTTP_EVE_TRUSTED'] == "Yes")
            $regionID = (int)$_SERVER['HTTP_EVE_REGIONID'];
        if(is_null($regionID))
            $regionID = $this->app->mapSolarSystems->getAllByID($this->app->CoreManager->getCharacterLocation($characterID))['regionID'];
        if(is_null($regionID) || $regionID == 0)
            $regionID = 10000029;

        $char = $this->app->CoreManager->getCharacter($characterID);


        do {

            if(!$char->hasPermission("readIntel", "alliance") && !$char->hasPermission("readIntel", "corporation")) break;

            $systemRows = $this->db->query("SELECT solarSystemID as id FROM mapSolarSystems WHERE regionID = :regionID", array(":regionID" => $regionID));
            foreach ($systemRows as $systemRow) {
                $sys = array("systemID" => $systemRow['id'], "hostilecount" => 0, "lastreport" => 0);
                // get members
                $members = $this->db->query(
                    "SELECT characterID as id,characterName as name,corporationID,allianceID,submitterID,timestamp
                    FROM easTracker WHERE
                        easTracker.locationID = :locationID AND easTracker.timestamp > :ts AND
                        easTracker.timestamp =
                            (SELECT timestamp FROM easTracker as t WHERE
                                t.characterID = easTracker.characterID ORDER BY t.timestamp DESC LIMIT 1) AND easTracker.timestamp > :ts ORDER BY easTracker.characterName ASC",// LIMIT 100",
                    array(
                        ":locationID" => $systemRow['id'],
                        ":ts" => $this->maxIntelAge
                    )
                );
                foreach ($members as &$member) {

                    $submitter = $this->app->CoreManager->getCharacter($member['submitterID']);
                    if(
                        $char->getId() != $submitter->getId() && 
                        $char->getCorpId() != $submitter->getCorpId() && 
                        ($char->getAlliId() == 0 || $char->getAlliId() != $submitter->getAlliId()) && 
                        $submitter->derivedStanding($char) <= 0 && 
                        !$char->hasPermission("bjhjhlajkhlajksdhflkjasdhflFuckingOpsec")
                    ) continue;

                    $standing = $char->derivedStanding(new CoreCharacter($this->app, array("characterID" => $member['id'], "corporationID" => $member['corporationID'], "allianceID" => $member['allianceID'])));

                    if($standing <= 0) {
                        $sys['hostilecount']++;
                        if($sys['lastreport'] < (int)$member['timestamp'])
                            $sys['lastreport'] = (int)$member['timestamp'];
                    }
                }
                array_push($intel, $sys);
            }

        } while(0);

        return $intel;
    }

    public function setCharacterInfo ($characterID, $info) {
        $resp = array("state" => "error");
        if(isset($_SESSION["loggedIn"])) {

            do {

                $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);

                if(!$character->hasPermission("writeIntel", "alliance") && !$character->hasPermission("writeIntel", "corporation")) break;

                $this->db->execute("INSERT INTO easTrackerInfo (submitterID, characterID, info, timestamp) VALUES (:submitterID, :characterID, :info, :ts)",
                    array(
                        ":submitterID"  => $character->getId(),
                        ":characterID"  => $characterID,
                        ":info"         => $info,
                        ":ts"           => time()
                    )
                );

                $resp['state'] = "success";

            } while (0);
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

}
