<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreManager {

    private $app;
    private $db;
    private $config;

    function __construct(RenaApp $app) {
        $this->app = $app;
        $this->db = $this->app->Db;
        $this->config = $this->app->baseConfig;
    }

    public function login ($characterID) {
        $char = $this->createCharacter($characterID);

        if(isset($_SERVER["HTTP_X_REAL_IP"]))
            $this->createLog("login", array("characterID" => $characterID, "ip" => $_SERVER["HTTP_X_REAL_IP"]));

        if(isset($_SESSION['characterID'])) {
            $char->setUser($this->createCharacter($_SESSION['characterID'])->getUser());
        } else {
            if($char->getUser() == null) {
                $user = $this->createUser();
                $char->setUser($user->getId());
            }
        }

        // Set the session
        $_SESSION["characterName"] = $char->getName();
        $_SESSION["characterID"] = $char->getId();
        $_SESSION["loggedIn"] = true;
    }

    public function createUser () {
        return new CoreUser($this->app, array("id" => $this->db->execute("INSERT INTO easUsers (admin) VALUES (0)", array(), true)));
    }

    public function createCharacter ($characterID) {
        $char = null;
        //$apiChar = $this->app->EVEEVECharacterAffiliation->getData([$characterID])["result"]["characters"][0];
        $apiChar = $this->getCharacter($characterID, true)->jsonSerialize();
        $charRow = $this->db->queryRow("SELECT * FROM easCharacters WHERE characterID = :characterID", array(":characterID" => $characterID));
        if(!$charRow) {
            $this->db->execute("INSERT INTO easCharacters (characterID, characterName, corporationID, corporationName, allianceID, allianceName) VALUE (:characterID, :characterName, :corporationID, :corporationName, :allianceID, :allianceName)",
                array(":characterID"        => $apiChar['characterID'],
                      ":characterName"      => $apiChar['characterName'],
                      ":corporationID"      => $apiChar['corporationID'],
                      ":corporationName"    => $apiChar['corporationName'],
                      ":allianceID"         => $apiChar['allianceID'],
                      ":allianceName"       => $apiChar['allianceName'],
                ), true);
            $charRow = $this->db->queryRow("SELECT * FROM easCharacters WHERE characterID = :characterID", array(":characterID" => $characterID));
            $char = new CoreCharacter($this->app, $charRow);
            $char->setBaseGroups();
            $char->setBaseOptions();
        } else {
            $changed = false;
            $vars = array('characterID' => 'Id', 'characterName' => 'Name', 'corporationID' => 'CorpId', 'corporationName' => 'CorpName', 'allianceID' => 'AlliId', 'allianceName' => 'AlliName');
            $char = new CoreCharacter($this->app, $charRow);
            $ch = $this->charChanged($char, $apiChar);
            if($ch || count($char->getGroups()) == 0) {
                $char->setBaseGroups();
                $char->setBaseOptions();
            }
        }
        return $char;
    }

    protected $chars = array();
    public function getCharacter ($characterID, $bypassdb = false) {
        if(isset($this->chars[$characterID]))
            return $this->chars[$characterID];
        $charRow = $this->db->queryRow("SELECT * FROM easCharacters WHERE characterID = :characterID", array(":characterID" => $characterID));
        if($charRow && !$bypassdb) {
            $char = new CoreCharacter($this->app, $charRow);
            $this->chars[$characterID] = $char;
            return $char;
        } else {
            $ntCharRow = $this->db->queryRow("SELECT ntCharacter.id as characterID, ntCharacter.name as characterName, ntCharacter.corporation as corporationID, ntCorporation.name as corporationName, ntCorporation.alliance as allianceID, ntAlliance.name as allianceName, NULL as user FROM ntCharacter LEFT JOIN ntCorporation ON ntCharacter.corporation = ntCorporation.id LEFT JOIN ntAlliance ON ntCorporation.alliance = ntAlliance.id WHERE ntCharacter.id = :characterID AND UNIX_TIMESTAMP(ntCharacter.lastUpdateTimestampCA) > UNIX_TIMESTAMP(NOW()) - 86400", array(":characterID" => $characterID));
            if($ntCharRow) {
                $char = new CoreCharacter($this->app, $ntCharRow);
                $this->chars[$characterID] = $char;
                return $char;
            } else {
                $affDat = $this->app->EVEEVECharacterAffiliation->getData([$characterID]);
                if($affDat) {
                    $corp = $this->getCorporation($affDat['result']['characters'][0]['corporationID']);
                    $this->db->execute(
                        "INSERT INTO ntCharacter (id, name, corporation, lastUpdateTimestampCA)
                        VALUES (:characterID, :characterName, :corporationID, :lastUpdateTimestampCA)
                        ON DUPLICATE KEY
                        UPDATE ntCharacter.corporation = VALUES(ntCharacter.corporation), ntCharacter.lastUpdateTimestampCA = VALUES(ntCharacter.lastUpdateTimestampCA)",
                        array(
                            ":characterID"              => $affDat['result']['characters'][0]['characterID'],
                            ":characterName"            => $affDat['result']['characters'][0]['characterName'],
                            ":corporationID"            => $affDat['result']['characters'][0]['corporationID'],
                            ":lastUpdateTimestampCA"    => $affDat['currentTime']
                        )
                    );
                    $char = new CoreCharacter($this->app, $affDat['result']['characters'][0]);
                    $this->chars[$characterID] = $char;
                    return $char;
                }
            }
        }
        return null;
    }

    public function getCharacters ($ids) {
        $reqChars = array();
        $retChars = array();
        foreach ($ids as $id) {
            if(isset($this->chars[$id])) {
                $retChars[$id] = $this->chars[$id];
            } else {
                $reqChars[] = $id;
            }
        }
        $dbChars = array();
        if(count($reqChars) > 0) {
            $impIDs = implode(",", $reqChars);
            $charRows = $this->db->query(
                "SELECT
                    ntCharacter.id as characterID,
                    ntCharacter.name as characterName,
                    ntCharacter.corporation as corporationID,
                    ntCorporation.name as corporationName,
                    ntCorporation.alliance as allianceID,
                    ntAlliance.name as allianceName,
                    NULL as user
                FROM ntCharacter
                LEFT JOIN ntCorporation
                ON ntCharacter.corporation = ntCorporation.id
                LEFT JOIN ntAlliance
                ON ntCorporation.alliance = ntAlliance.id
                WHERE ntCharacter.id IN ($impIDs) AND UNIX_TIMESTAMP(ntCharacter.lastUpdateTimestampCA) > UNIX_TIMESTAMP(NOW()) - 86400"
            );
            foreach ($charRows as $charRow) {
                $dbChars[] = $charRow['characterID'];
                $char = new CoreCharacter($this->app, $charRow);
                $this->chars[$charRow['characterID']] = $char;
                $retChars[$charRow['characterID']] = $char;
            }
        }
        $leftChars = array_diff($reqChars, $dbChars);

        $chunkedChars = array_chunk($leftChars, 250);

        $insertChars = [];
        $corpIDs = [];

        foreach ($chunkedChars as $chunkedCharList) {
            $dat = $this->app->EVEEVECharacterAffiliation->getData($chunkedCharList);
            $tmpChars = $dat['result']['characters'];
            foreach ($tmpChars as $tmpChar) {
                $char = new CoreCharacter($this->app, $tmpChar);
                $this->chars[$char->getId()] = $char;
                $retChars[$char->getId()] = $char;
                $insertChars[] = array(
                        ":characterID"              => $tmpChar['characterID'],
                        ":characterName"            => $tmpChar['characterName'],
                        ":corporationID"            => $tmpChar['corporationID'],
                        ":lastUpdateTimestampCA"    => $dat['currentTime']
                    );
                $corpIDs[] = $tmpChar['corporationID'];
            }
        }

        $corps = $this->getCorporations($corpIDs);

        if(count($insertChars) > 0)
            $this->db->multiInsert("INSERT INTO ntCharacter (id, name, corporation, lastUpdateTimestampCA)", $insertChars, "ON DUPLICATE KEY UPDATE ntCharacter.corporation = VALUES(ntCharacter.corporation), ntCharacter.lastUpdateTimestampCA = VALUES(ntCharacter.lastUpdateTimestampCA)");

        return $retChars;
    }

    public function getAllCharacters () {
        $charRows = $this->db->query("SELECT characterID FROM easCharacters");
        $ids = [];
        foreach ($charRows as $charRow)
            $ids[] = $charRow['characterID'];
        $this->getCharacters($ids);
        return $this->chars;
    }

    protected $corps = array();
    public function getCorporation ($corporationID) {
        if(isset($this->corps[$corporationID]))
            return $this->corps[$corporationID];
        $corporationRow = $this->db->queryRow("SELECT * FROM ntCorporation WHERE id = :corporationID", array(":corporationID" => $corporationID));
        if($corporationRow) {
            $corp = new CoreCorporation($this->app, $corporationRow);
            $this->corps[$corporationID] = $corp;
            return $corp;
        } else {
            $data = $corpApi = $this->app->EVECorporationCorporationSheet->getData(null, null, $corporationID);
            if($data) {
                $corpApi = $data['result'];
                try {
                    $this->db->execute(
                            "INSERT INTO ntCorporation (id, shortName, name, ceoCharacterID, alliance)
                            VALUES (:corporationID, :shortName, :corporationName, :ceoCharacterID, :alliance)
                            ON DUPLICATE KEY
                            UPDATE ntCorporation.ceoCharacterID = VALUES(ntCorporation.ceoCharacterID), ntCorporation.ceoCharacterID = VALUES(ntCorporation.ceoCharacterID)",
                            array(
                                ":corporationID"    => $corpApi['corporationID'],
                                ":shortName"        => $corpApi['ticker'],
                                ":corporationName"  => $corpApi['corporationName'],
                                ":ceoCharacterID"   => $corpApi['ceoID'],
                                ":alliance"         => $corpApi['allianceID'] != 0 ? $corpApi['allianceID'] : null,
                            )
                        );
                } catch(Exception $e) {
                    return json_encode($corpApi['allianceID']);
                }
                $corp = new CoreCorporation($this->app,
                    array(
                        "id"                => $corpApi['corporationID'],
                        "shortName"         => $corpApi['ticker'],
                        "name"              => $corpApi['corporationName'],
                        "ceoCharacterID"    => $corpApi['ceoID'],
                        "alliance"          => $corpApi['allianceID'],
                        "npc"               => null
                    )
                );
                $this->corps[$corporationID] = $corp;
                return $corp;
            }
        }
        return null;
    }

    public function getCorporations ($ids) {
        $reqCorps = array();
        $retCorps = array();
        foreach ($ids as $id) {
            if(isset($this->corps[$id])) {
                $retCorps[$id] = $this->corps[$id];
            } else {
                $reqCorps[] = $id;
            }
        }
        $dbCorps = array();
        if(count($reqCorps) > 0) {
            $impIDs = implode(",", $reqCorps);
            $corpRows = $this->db->query("SELECT * FROM ntCorporation WHERE ntCorporation.id IN ($impIDs)");
            foreach ($corpRows as $corpRow) {
                $dbCorps[] = $corpRow['id'];
                $corp = new CoreCorporation($this->app, $corpRow);
                $this->corps[$corpRow['id']] = $corp;
                $retCorps[$corpRow['id']] = $corp;
            }
        }
        $leftCorps = array_diff($reqCorps, $dbCorps);

        $insertCorps = [];
        $alliIDs = [];

        foreach ($leftCorps as $leftCorp) {
            $data = $corpApi = $this->app->EVECorporationCorporationSheet->getData(null, null, $leftCorp);
            $corpApi = $data['result'];
            $corp = new CoreCorporation($this->app,
                array(
                    "id"                => $corpApi['corporationID'],
                    "shortName"         => $corpApi['ticker'],
                    "name"              => $corpApi['corporationName'],
                    "ceoCharacterID"    => $corpApi['ceoID'],
                    "alliance"          => $corpApi['allianceID'],
                    "npc"               => null
                )
            );
            $this->corps[$corp->getId()] = $corp;
            $retCorps[$corp->getId()] = $corp;
            $insertCorps[] = array(
                    ":corporationID"    => $corpApi['corporationID'],
                    ":shortName"        => $corpApi['ticker'],
                    ":corporationName"  => $corpApi['corporationName'],
                    ":ceoCharacterID"   => $corpApi['ceoID'],
                    ":alliance"         => $corpApi['allianceID'] != 0 ? $corpApi['allianceID'] : null
                );
            if(!in_array($corpApi['alliance'], $alliIDs))
                $alliIDs[] = $corpApi['alliance'];
        }

        if(count($insertCorps) > 0)
            $this->db->multiInsert("INSERT INTO ntCorporation (id, shortName, name, ceoCharacterID, alliance)", $insertCorps, "ON DUPLICATE KEY UPDATE ntCorporation.ceoCharacterID = VALUES(ntCorporation.ceoCharacterID), ntCorporation.ceoCharacterID = VALUES(ntCorporation.ceoCharacterID)");

        return $retCorps;
    }

    protected $allis = array();
    public function getAlliance ($allianceID) {
        if(isset($this->allis[$allianceID]))
            return $this->allis[$allianceID];
        $allianceRow = $this->db->queryRow("SELECT * FROM ntAlliance WHERE id = :allianceID", array(":allianceID" => $allianceID));
        if($allianceRow) {
            $alli = new CoreAlliance($this->app, $allianceRow);
            $this->allis[$allianceID] = $alli;
            return $alli;
        }
        return null;
    }

    public function getNotification ($notificationID) {
        $notification = $this->db->queryRow("SELECT * FROM easNotifications WHERE id = :id", array(":id" => $notificationID));
        if($notification)
            return new CoreNotification($this->app, $notification);
        return null;
    }

    public function getNotificationByLocation ($locationID) {
        $notification = $this->db->queryRow("SELECT * FROM easNotifications WHERE locationID = :locationID ORDER BY id DESC", array(":locationID" => $locationID));
        if($notification)
            return new CoreNotification($this->app, $notification);
        return null;
    }

    public function deleteNotification ($notificationID) {
        $this->db->execute("DELETE FROM easNotifications WHERE id = :id", array(":id" => $notificationID));
    }

    protected $groupsByID = array();
    protected $groupsByName = array();
    public function getGroup ($grp) {
        if(is_int($grp) && isset($this->groupsByID[$grp])) {
            return $this->groupsByID[$grp];
        } else if(is_string($grp) && isset($this->groupsByName[$grp])) {
            return $this->groupsByName[$grp];
        }
        $groupRow = array();
        if(is_int($grp)) {
            $groupRow = $this->db->queryRow("SELECT * FROM easGroups WHERE id = :bi", array(":bi" => $grp));
        } else if(is_string($grp)) {
            $groupRow = $this->db->queryRow("SELECT * FROM easGroups WHERE name = :bi", array(":bi" => $grp));
        }
        if($groupRow) {
            $group = new CoreGroup($this->app, $groupRow);
            $this->groupsByID[$group->getId()] = $group;
            $this->groupsByName[$group->getName()] = $group;
            return $group;
        }
        return null;
    }

    public function getGroupsByScope ($scope) {
        $groups = array();
        $groupRows = $this->db->query("SELECT id FROM easGroups WHERE scope = :scope", array(":scope" => $scope));
        foreach ($groupRows as $groupRow)
            array_push($groups, $this->getGroup($groupRow['id']));
        return $groups;
    }

    public function getGroupsByOwner ($owner = null) {
        $groups = array();
        $groupRows = $this->db->query("SELECT id FROM easGroups WHERE owner = :owner OR owner IS NULL", array(":owner" => $owner));
        foreach ($groupRows as $groupRow)
            array_push($groups, $this->getGroup((int)$groupRow['id']));
        return $groups;
    }

    public function getGroupsByOwnerAndScope ($owner, $scope) {
        $groups = array();
        $groupRows = $this->db->query("SELECT id FROM easGroups WHERE (owner = :owner OR (owner IS NULL AND custom = 1)) AND scope = :scope", array(":owner" => $owner, ":scope" => $scope));
        foreach ($groupRows as $groupRow)
            array_push($groups, $this->getGroup((int)$groupRow['id']));
        return $groups;
    }

    protected $permissionsByID = array();
    protected $permissionsByName = array();
    public function getPermission ($perm, $scope = null) {
        if(is_int($perm) && isset($this->permissionsByID[$perm])) {
            return $this->permissionsByID[$perm];
        } else if(is_string($perm) && isset($this->permissionsByName[$perm])) {
            $perms = $this->permissionsByName[$perm];
            if(is_null($scope) && count($perms) > 0) {
                return $perms[0];
            } else if(!is_null($scope) && count($perms) > 0) {
                foreach ($perms as $tmpperm)
                    if($tmpperm->getScope() == $scope)
                        return $tmpperm;
            }
        }
        $permissionRow = array();
        if(is_int($perm)) {
            $permissionRow = $this->db->queryRow("SELECT * FROM easPermissions WHERE id = :bi", array(":bi" => $perm));
        } else if(is_string($perm)) {
            if(is_null($scope)) {
                $permissionRow = $this->db->queryRow("SELECT * FROM easPermissions WHERE name = :bi", array(":bi" => $perm));
            } else {
                $permissionRow = $this->db->queryRow("SELECT * FROM easPermissions WHERE name = :bi AND scope = :scope", array(":bi" => $perm, ":scope" => $scope));
            }
        }
        if($permissionRow) {
            $permission = new CorePermission($this->app, $permissionRow);
            $this->permissionsByID[$permission->getId()] = $permission;
            if(!isset($this->permissionsByName[$permission->getName()])) $this->permissionsByName[$permission->getName()] = array();
            $this->permissionsByName[$permission->getName()][] = $permission;
            return $permission;
        }
        return null;
    }

    public function getPermissionsByScope ($scope) {
        $permissions = array();
        $permissionRows = $this->db->query("SELECT id FROM easPermissions WHERE scope = :scope", array(":scope" => $scope));
        foreach ($permissionRows as $permissionRow)
            array_push($permissions, $this->getPermission((int)$permissionRow['id']));
        return $permissions;
    }

    protected $users = array();
    public function getUser ($userID) {
        if(isset($this->users[$userID]))
            return $this->user[$userID];
        $userRow = $this->db->queryRow("SELECT * FROM easUsers WHERE id = :userID", array(":userID" => $userID));
        if($userRow) {
            $user = new CoreUser($this->app, $userRow);
            $this->users[$userID] = $user;
            return $user;
        }
        return null;
    }

    public function getUserByToken ($token) {
        foreach ($this->users as $user)
            if($user->getAuthToken() == $token)
                return $user;
        $userRow = $this->db->queryRow("SELECT * FROM easUsers WHERE authtoken IS NOT NULL AND authtoken = :authtoken", array(":authtoken" => $token));
        if($userRow) {
            $user = new CoreUser($this->app, $userRow);
            $this->users[$user->getId()] = $user;
            return $user;
        }
        return null;
    }

    public function getFleetParticipant ($fleetparticipant) {
        $character = $this->getCharacter($fleetparticipant['characterID']);
        if(is_null($character)) return null;
        $characterData = $character->getData();
        $characterData['confirmed'] = $fleetparticipant['confirmed'];
        $cfleetparticipant = new CoreFleetParticipant($this->app, $characterData);
        return $cfleetparticipant;
    }

    protected $fleets = array();
    public function getFleet ($fleetID) {
        if(isset($this->fleets[$fleetID]))
            return $this->fleets[$fleetID];
        $fleetRow = $this->db->queryRow("SELECT * FROM easFleets WHERE id = :fleetID", array(":fleetID" => $fleetID));
        if($fleetRow) {
            $fleet = new CoreFleet($this->app, $fleetRow);
            $this->fleets[$fleet->getId()] = $fleet;
            return $fleet;
        }
        return null;
    }

    public function getFleetByHash ($hash) {
        foreach ($this->fleets as $fleet)
            if($fleet->getHash() == $hash)
                return $fleet;
        $fleetRow = $this->db->queryRow("SELECT * FROM easFleets WHERE hash = :hash", array(":hash" => $hash));
        if($fleetRow) {
            $fleet = new CoreFleet($this->app, $fleetRow);
            $this->fleets[$fleet->getId()] = $fleet;
            return $fleet;
        }
        return null;
    }

    public function createFleet ($scope, $name, $comment, $creator, $expiresin, $participants) {
        $id = $this->db->execute("INSERT INTO easFleets (scope, name, comment, creator, time, expires, hash) VALUES (:scope, :name, :comment, :creator, :time, :expires, :hash)",
            array(
                ":scope"    => $scope,
                ":name"     => $name,
                ":comment"  => $comment,
                ":creator"  => $creator,
                ":time"     => time(),
                ":expires"  => time() + (60*60*$expiresin),
                ":hash"     => md5($creator.$name.$comment.time().$this->config->getConfig("fleetsalt", "secrets"))
            ), true
        );

        $character = $this->getCharacter($creator);

		// participants
		$participantsArr = str_replace("%20", " ", $participants);
		$participantsArr = explode(",", $participantsArr);

		$chunkedParticipants = array_chunk($participantsArr, 100);
		$idsFromAPI = array();
		for($i = 0; $i < count($chunkedParticipants); $i++) {
			$idsFromAPI = array_merge($idsFromAPI, $this->app->EVEEVECharacterID->getData($chunkedParticipants[$i])['result']['characters']);
		}

		$idsFromAPISorted = array();
		foreach ($idsFromAPI as $idFromAPI)
			array_push($idsFromAPISorted, (int)$idFromAPI['characterID']);

        // get affiliations from api
        $chunkedIdsFromAPI = array_chunk($idsFromAPISorted, 100);
        $affs = array();
        for($i = 0; $i < count($chunkedIdsFromAPI); $i++)
            $affs = array_merge($affs, $this->app->EVEEVECharacterAffiliation->getData($chunkedIdsFromAPI[$i])['result']['characters']);

        $affsSorted = array();
        foreach($affs as $aff)
            $affsSorted[$aff['characterID']] = $aff;


		for($i = 0; $i < count($idsFromAPISorted); $i++) {
            if(
                ($scope == "corporation" && $character->getCorpId() == $affsSorted[$idsFromAPISorted[$i]]['corporationID']) ||
                ($scope == "alliance" && $character->getAlliId() == $affsSorted[$idsFromAPISorted[$i]]['allianceID']) ||
                ($scope == "blue" && $character->getAlliId() != 0 && $character->getCCorporation()->getCAlliance()->hasStandingsTowards($this->app->CoreManager->getCharacter($idsFromAPISorted[$i])))
            ) {
                $this->db->execute("INSERT INTO easFleetParticipants (fleetID, characterID, confirmed) VALUE (:fleetID, :characterID, 0)", array(":fleetID" => $id, ":characterID" => $idsFromAPISorted[$i]));
            }
        }
		if(in_array($creator, $idsFromAPISorted)) {
			$this->db->execute("UPDATE easFleetParticipants SET confirmed = 1 WHERE fleetID = :fleetID AND characterID = :characterID", array(":fleetID" => $id, ":characterID" => $creator));
		} else {
			$this->db->execute("INSERT INTO easFleetParticipants (fleetID, characterID, confirmed) VALUE (:fleetID, :characterID, 1)", array(":fleetID" => $id, ":characterID" => $creator));
		}
        return $this->getFleet($id);
    }

    protected $items = array();
    public function getItem ($itemID) {
        if(isset($this->items[$itemID]))
            return $this->items[$itemID];
        $itemRow = $this->db->queryRow("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntItem.lastUpdateTimestamp FROM ntItem LEFT JOIN ntLocation ON ntItem.itemID = ntLocation.itemID WHERE ntItem.itemID = :itemID", array(":itemID" => $itemID));
        if($itemRow) {
            $item = new CoreItem($this->app, $itemRow);
            $this->items[$itemID] = $item;
            return $item;
        }
        return null;
    }

    public function getItemsByLocation ($locationID) {
        $items = array();
        $itemRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntItem.lastUpdateTimestamp FROM ntItem LEFT JOIN ntLocation ON ntItem.itemID = ntLocation.itemID WHERE ntItem.locationID = :locationID", array(":locationID" => $locationID));
        foreach ($itemRows as $itemRow) {
            $item = new CoreItem($this->app, $itemRow);
            $this->items[$item->getId()] = $item;
            array_push($items, $item);
        }
        return $items;
    }

    protected $itemtypes = array();
    public function getItemType ($itemTypeID) {
        if(isset($this->itemtypes[$itemTypeID]))
            return $this->itemtypes[$itemTypeID];
        $itemTypeRow = $this->db->queryRow("SELECT * FROM invTypes WHERE typeID = :typeID", array(":typeID" => $itemTypeID));
        if($itemTypeRow) {
            $itemtype = new CoreItemType($this->app, $itemTypeRow);
            $this->itemtypes[$itemTypeID] = $itemtype;
            return $itemtype;
        }
        return null;
    }

    protected $locations = array();
    public function getLocation ($locationID, $invName = false) {
        if(isset($this->locations[$locationID]))
            return $this->locations[$locationID];
        if($invName) {
            $invnameLocRow = $this->db->queryRow("SELECT itemName as name, itemID as id FROM invNames WHERE itemID = :itemID", array(":itemID" => $locationID));
            if($invnameLocRow) {
                $invnameLoc = new CoreLocation($this->app, $invnameLocRow);
                $this->locations[$locationID] = $invnameLoc;
                return $invnameLoc;
            }
        }
        return null;
    }

    public function addLocation ($newlocation) {
        if(isset($this->locations[$newlocation->getId()])) return;
        $this->locations[$newlocation->getId()] = $newlocation;
    }

    protected $containers = array();
    public function getContainer ($containerID) {
        if(isset($this->containers[$containerID]))
            return $this->containers[$containerID];
        $containerRow = $this->db->queryRow("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z,ntItem.lastUpdateTimestamp FROM ntItem,ntLocation WHERE ntItem.itemID = :itemID AND ntLocation.itemID = ntItem.itemID", array(":itemID" => $containerID));
        if($containerRow) {
            $conti = new CoreContainer($this->app, $containerRow);
            $this->containers[$containerID] = $conti;
            return $conti;
        }
        return null;
    }

    public function getContainersByLocation ($locationID) {
        $containers = array();
        $containerRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z,ntItem.lastUpdateTimestamp FROM ntItem,ntLocation WHERE ntItem.locationID = :locationID AND ntLocation.itemID = ntItem.itemID", array(":locationID" => $locationID));
        foreach ($containerRows as $containerRow) {
            $container = new CoreContainer($this->app, $containerRow);
            $this->containers[$container->getId()] = $container;
            array_push($containers, $container);
        }
        return $containers;
    }

    protected $controltowers = array();
    public function getControltower ($towerID) {
        if(isset($this->controltowers[$towerID]))
            return $this->controltowers[$towerID];
        $controltowerRow = $this->db->queryRow("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z,ntItemStarbase.state,ntItemStarbase.moonID,ntItem.lastUpdateTimestamp FROM ntItem,ntLocation,ntItemStarbase WHERE ntItem.itemID = :itemID AND ntLocation.itemID = ntItem.itemID AND ntItemStarbase.itemID = ntItem.itemID", array(":itemID" => $towerID));
        if($controltowerRow) {
            $controlTower = new CoreControltower($this->app, $controltowerRow);
            $this->controltowers[$towerID] = $controlTower;
            return $controlTower;
        }
        return null;
    }

    protected $timers = array();
    public function getTimer ($timerID) {
        if(isset($this->timers[$timerID]))
            return $this->timers[$timerID];
        $timerRow = $this->db->queryRow("SELECT * FROM easTimers WHERE id = :timerID", array(":timerID" => $timerID));
        if($timerRow) {
            $ctimer = new CoreTimer($this->app, $timerRow);
            $this->timers[$timerID] = $ctimer;
            return $ctimer;
        }
        return null;
    }

    public function createTimer ($scope, $creatorID, $ownerID, $typeID, $locationID, $rf, $comment, $timestamp) {
        $id = $this->db->execute("INSERT INTO easTimers (scope, creatorID, ownerID, typeID, locationID, rf, comment, timestamp) VALUES (:scope, :creatorID, :ownerID, :typeID, :locationID, :rf, :comment, :timestamp)",
            array(
            ":scope"      => $scope,
            ":creatorID"  => $creatorID,
            ":ownerID"    => $ownerID,
            ":typeID"     => $typeID,
            ":locationID" => $locationID,
            ":rf"         => $rf,
            ":comment"    => $comment,
            ":timestamp"  => $timestamp,
            ), true
        );
        return $this->getTimer($id);
    }

    public function getTimers ($creatorID) {
        $timers = array();
        $timerRows = $this->db->query("SELECT * FROM easTimers WHERE creatorID = :creatorID ORDER BY timestamp ASC", array(":creatorID" => $creatorID));
        foreach ($timerRows as $timerRow)
            array_push($timers, new CoreTimer($this->app, $timerRow));
        return $timers;
    }

    public function charChanged ($char, $apiChar) {
        $changed = false;
        $vars = array('characterID' => 'Id', 'characterName' => 'Name', 'corporationID' => 'CorpId', 'corporationName' => 'CorpName', 'allianceID' => 'AlliId', 'allianceName' => 'AlliName');
        foreach ($vars as $var => $mName) {
            if($char->{'get'.$mName}() != $apiChar[$var]) {
                $char->{'set'.$mName}($apiChar[$var]);
                $changed = true;
            }
        }
        return $changed;
    }

    public function createGroup ($groupName, $scope, $owner = null, $custom = 0) {
        $id = $this->db->execute("INSERT INTO easGroups (name, scope, owner, custom) VALUES (:name, :scope, :owner, :custom)",
            array(
                ":name" => $groupName,
                ":scope" => $scope,
                ":owner" => $owner,
                ":custom" => $custom), true);
        return new CoreGroup($this->app, $this->db->queryRow("SELECT * FROM easGroups WHERE id = :id", array(":id" => $id)));
    }

    public function getDGMAttribute ($typeID, $attributeID) {
        return $this->db->queryRow("SELECT * FROM dgmTypeAttributes WHERE typeID = :typeID AND attributeID = :attributeID", array(":typeID" => $typeID, ":attributeID" => $attributeID));
    }

    public function entityExists ($name) {
        $res = $this->db->queryRow("SELECT * FROM (SELECT id,name FROM ntCorporation UNION SELECT id,name FROM ntAlliance) c WHERE c.name = :name", array(":name" => $name));
        if($res)
            return true;
        return false;
    }

    public function getCharacterLocation ($characterID) {
        return $this->db->queryField("SELECT locationID FROM easTracker WHERE characterID = :characterID ORDER BY timestamp DESC LIMIT 1", "locationID", array(":characterID" => $characterID));
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function createLog ($type = "default", $data = array(), $time = null) {
        $this->db->execute(
            "INSERT INTO easLogs (type,data,timestamp)
            VALUES (:type, :data, :ts)",
            array(
                ":type" => $type,
                "data" => json_encode($data),
                ":ts" => is_null($time) ? time() : $time
            )
        );
    }

    public function readSession ($id) {
        session_decode(file_get_contents(session_save_path()."/sess_".$id));
    }

}
