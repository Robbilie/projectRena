<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class IntelController
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
        if(isset($_SESSION['loggedin'])) {

            // set system id
            if(is_null($systemID) && isset($_SERVER['HTTP_EVE_TRUSTED']) && $_SERVER['HTTP_EVE_TRUSTED'] == "Yes")
                $systemID = (int)$_SERVER['HTTP_EVE_SOLARSYSTEMID'];
            if(is_null($systemID))
                $systemID = $this->app->CoreManager->getCharacterLocation($_SESSION['characterID']);
            if(is_null($systemID) || $systemID == 0)
                $systemID = 30002489;

            if(isset($_GET['hash']) && $_GET['hash'] != "") {
                $timeout = 15000000;
                $interval = 500000;
                while($timeout > 0) {

                    session_start();
                    $charid = $_SESSION['characterID'];
                    session_write_close();

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
                        ":characterName" => $char->getCharName(),
                        ":corporationID" => $char->getCorpId(),
                        ":allianceID" => $char->getAlliId(),
                        ":ts" => time()
                    )
                );
        }

        // initial values
        $solarSystem = $this->app->mapSolarSystems->getAllByID($systemID);
        $intel['state'] = 1;
        $intel['status'] = "Online";
        $intel['systemID'] = $solarSystem['solarSystemID'];
        $intel['systemName'] = $solarSystem['solarSystemName'];
        $intel['regionID'] = $solarSystem['regionID'];
        $intel['regionName'] = $this->app->mapRegions->getAllByID($solarSystem['regionID'])['regionName'];
        $intel['hostilecount'] = 0;

        // get members
        $members = $this->db->query(
            "SELECT characterID as id,characterName as name,corporationID,allianceID,submitterID FROM easTracker WHERE
                easTracker.locationID = :locationID AND
                easTracker.timestamp =
                    (SELECT timestamp FROM easTracker as t WHERE
                        t.characterID = easTracker.characterID ORDER BY t.timestamp DESC LIMIT 1) AND easTracker.timestamp > :ts ORDER BY easTracker.characterName ASC",// LIMIT 100",
            array(
                ":locationID" => $systemID,
                ":ts" => /*time() - (60*60*24)*/ 0 // 0 if all should be intelled
            )
        );

        if(count($members) <= 50) {
            $intel['membertype'] = "characters";
            foreach ($members as &$member) {
              if(!$char->getCAlliance()->hasStandingsTowards($this->app->CoreManager->getCharacter($member['submitterID']))) continue;
                if(!$char->getCAlliance()->hasStandingsTowards($this->app->CoreManager->getCharacter($member['id']))) {
                  $member['standing'] = "negative";
                  $intel['hostilecount']++;
                } else {
                  $member['standing'] = "positive";
                }
            }
        } else {
            $intel['membertype'] = "alliances";
            $alliances = array();
            foreach ($members as $member) {
              if(!$char->getCAlliance()->hasStandingsTowards($this->app->CoreManager->getCharacter($member['submitterID']))) continue;
                //if($this->haveStandings($characterID, $member['submitterID']) <= 0) continue;
                if(is_null($alliances[$member['allianceID']]))
                    $alliances[$member['allianceID']] = array();
                array_push($alliances[$member['allianceID']], $member);
            }
            $alliancesSorted = array();
            foreach ($alliances as $key => $alliance) {
                if(!$char->getCAlliance()->hasStandingsTowards($this->app->CoreManager->getAlliance($key)->getExecCorp()->getCEOChar())) {
                  array_push($alliancesSorted,
                    array(
                      "id" => $key,
                      "name" => $this->app->CoreManager->getAlliance($key)->getName(),
                      "count" => count($alliance),
                      "standing" => "negative"
                    )
                  );
                  $intel['hostilecount'] += count($alliance);
                } else {
                    array_push($alliancesSorted,
                      array(
                        "id" => $key,
                        "name" => $this->app->CoreManager->getAlliance($key)->getName(),
                        "count" => count($alliance),
                        "standing" => "positive"
                      )
                    );
                }

            }
            $members = $alliancesSorted;
        }

        $intel['members'] = $members;

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
                    $_SESSION['loggedin'] = true;
                    $_SESSION['characterID'] = $char->getCharId();
                    $_SESSION['characterName'] = $char->getCharName();
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
        if(isset($_SESSION['loggedin'])) {
            // set system id
            if(is_null($systemID) && isset($_SERVER['HTTP_EVE_TRUSTED']) && $_SERVER['HTTP_EVE_TRUSTED'] == "Yes")
                $systemID = (int)$_SERVER['HTTP_EVE_SOLARSYSTEMID'];
            if(is_null($systemID))
                $systemID = $this->app->CoreManager->getCharacterLocation($_SESSION['characterID']);
            if(is_null($systemID) || $systemID == 0)
                $systemID = 30002489;
            // local members
            $local = str_replace("%20", " ", $this->app->request->post('local'));
            $local = explode(",", $local);

            $charid = $_SESSION['characterID'];
            session_write_close();


            // get ids from api
            $chunkedLocal = array_chunk($local, 100);
            $idsFromAPI = array();
            for($i = 0; $i < count($chunkedLocal); $i++) {
                $idsFromAPI = array_merge($idsFromAPI, $this->app->EVEEVECharacterID->getData($chunkedLocal[$i])['result']['characters']);
            }

            $idsFromAPISorted = array();
            foreach ($idsFromAPI as $idFromAPI)
                array_push($idsFromAPISorted, $idFromAPI['characterID']);

            // get affiliations from api
            $chunkedIdsFromAPI = array_chunk($idsFromAPISorted, 100);
            $affs = array();
            for($i = 0; $i < count($chunkedIdsFromAPI); $i++) {
                $affs = array_merge($affs, $this->app->EVEEVECharacterAffiliation->getData($chunkedIdsFromAPI[$i])['result']['characters']);
            }

            $affsSorted = array();
            foreach($affs as $aff)
                $affsSorted[$aff['characterID']] = $aff;

            // get chars in system
            $charRows = $this->db->query(
                "SELECT * FROM easTracker WHERE
                    easTracker.locationID = :locationID AND
                    easTracker.timestamp =
                        (SELECT timestamp FROM easTracker as t WHERE
                            t.characterID = easTracker.characterID ORDER BY t.timestamp DESC LIMIT 1)",
                array(":locationID" => $systemID)
            );

            // get ids and data from in system
            $charIDs = array();
            $charDat = array();
            foreach($charRows as $charRow) {
                array_push($charIDs, $charRow['characterID']);
                $charDat[$charRow['characterID']] = $charRow;
            }

            // get those not in system anymore
            $dif = array_diff($charIDs, $idsFromAPISorted);

            // move old chars to null system
            foreach($dif as $d)
                $this->db->execute(
                    "INSERT INTO easTracker
                        (locationID, submitterID, characterID, characterName, corporationID, allianceID, timestamp)
                    VALUES
                        (:locationID, :submitterID, :characterID, :characterName, :corporationID, :allianceID, :ts)",
                    array(
                        ":locationID" => "null",
                        ":submitterID" => $charid,
                        ":characterID" => $charDat[$d]['characterID'],
                        ":characterName" => $charDat[$d]['characterName'],
                        ":corporationID" => $charDat[$d]['corporationID'],
                        ":allianceID" => $charDat[$d]['allianceID'],
                        ":ts" => time()
                    )
                );

            // mover new chars into the system
            foreach($idsFromAPISorted as $id) {
                $this->db->execute(
                    "INSERT INTO easTracker
                        (locationID, submitterID, characterID, characterName, corporationID, allianceID, timestamp)
                    VALUES
                        (:locationID, :submitterID, :characterID, :characterName, :corporationID, :allianceID, UNIX_TIMESTAMP(NOW()))",
                    array(
                        ":locationID" => $systemID,
                        ":submitterID" => $charid,
                        ":characterID" => $affsSorted[$id]['characterID'],
                        ":characterName" => $affsSorted[$id]['characterName'],
                        ":corporationID" => $affsSorted[$id]['corporationID'],
                        ":allianceID" => $affsSorted[$id]['allianceID']
                    )
                );
            }

            $response = array("state" => "success", "msg" => "");
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($response));
    }

    public function getRegionIntel ($pregionID = null) {
        $regionID = $pregionID;
        $intel = array();
        if(isset($_SESSION['loggedin'])) {

            // set system id
            if(is_null($regionID) && isset($_SERVER['HTTP_EVE_TRUSTED']) && $_SERVER['HTTP_EVE_TRUSTED'] == "Yes")
                $regionID = (int)$_SERVER['HTTP_EVE_REGIONID'];
            if(is_null($regionID))
                $regionID = $this->app->mapSolarSystems->getAllByID($this->app->CoreManager->getCharacterLocation($characterID))['regionID'];
            if(is_null($regionID) || $regionID == 0)
                $regionID = 10000029;

            if(isset($_GET['hash']) && $_GET['hash'] != "") {
                $timeout = 15000000;
                $interval = 500000;
                while($timeout > 0) {

                    session_start();
                    $charid = $_SESSION['characterID'];
                    session_write_close();

                    $intel = $this->getRegionIntelArray($regionID, $charid);


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

    function getRegionIntelArrayOld ($pregionID = null, $characterID) {
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

        $systemRows = $this->db->query("SELECT solarSystemID as id FROM mapSolarSystems WHERE regionID = :regionID", array(":regionID" => $regionID));
        foreach ($systemRows as $systemRow) {
            $tmparr = $this->getSystemIntelArray($systemRow['id'], $characterID);
            array_push($intel, array("systemID" => $tmparr['systemID'], "hostilecount" => $tmparr['hostilecount']));
        }

        return $intel;
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

        $systemRows = $this->db->query("SELECT solarSystemID as id FROM mapSolarSystems WHERE regionID = :regionID", array(":regionID" => $regionID));
        foreach ($systemRows as $systemRow) {
            $sys = array("systemID" => $systemRow['id'], "hostilecount" => 0);
            // get members
            $members = $this->db->query(
                "SELECT characterID as id,characterName as name,corporationID,allianceID FROM easTracker WHERE
                    easTracker.locationID = :locationID AND
                    easTracker.timestamp =
                        (SELECT timestamp FROM easTracker as t WHERE
                            t.characterID = easTracker.characterID ORDER BY t.timestamp DESC LIMIT 1) AND easTracker.timestamp > :ts ORDER BY easTracker.characterName ASC",// LIMIT 100",
                array(
                    ":locationID" => $systemRow['id'],
                    ":ts" => /*time() - (60*60*24)*/ 0 // 0 if all should be intelled
                )
            );
            foreach ($members as &$member) {
                if(!$char->getCAlliance()->hasStandingsTowards($this->app->CoreManager->getCharacter($member['id'])))
                  $sys['hostilecount']++;
            }
            array_push($intel, $sys);
        }

        return $intel;
    }

    public function haveStandings ($charOneID, $charTwoID) {
        $charOne = $this->app->CoreManager->getCharacter($charOneID);
        $charTwo = $this->app->CoreManager->getCharacter($charTwoID);
        $r = $this->db->queryField(
            "SELECT count(contactID) as cnt FROM ntContactList WHERE
                ownerID = :ownerID AND
                (
                    contactID = :characterID OR
                    contactID = :corporationID OR
                    contactID = :allianceID
                ) AND
                standing > 0",
            "cnt",
            array(
                ":ownerID" => $charTwo->getAlliId(),
                ":characterID" => $charOneID,
                ":corporationID" => $charOne->getCorpId(),
                ":allianceID" => $charOne->getAlliId()
            )
        );
        if($r == 0 && $charOne->getAlliId() != $charTwo->getAlliId()) {
            return 0;
        } else {
            return 1;
        }
    }
}
