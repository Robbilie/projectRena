<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreCharacter extends CoreEntity {

	protected $id;
	protected $user;
	protected $characterID;
	protected $characterName;
	protected $corporationID;
	protected $corporationName;
	protected $allianceID;
	protected $allianceName;

	protected $groups;
	protected $cgroups;

	protected $fleets;
	protected $cfleets;

	protected $userObj;
	protected $corp;
	protected $alliance;

	protected $items;
	protected $containers;

	protected $permissions;
	protected $cpermissions;

	protected $notifications;
	protected $cnotifications;

	protected $options;
	protected $verified;

	protected $apiData;

	// custom

	public function getCUser () {
		if(is_null($this->userObj))
			$this->userObj = $this->app->CoreManager->getUser($this->user);
		return $this->userObj;
	}

	public function getCCorporation () {
		if(is_null($this->corp))
			$this->corp = $this->app->CoreManager->getCorporation($this->corporationID);
		return $this->corp;
	}

	public function getCAlliance () {
		if(is_null($this->alliance))
			$this->alliance = $this->app->CoreManager->getAlliance($this->allianceID);
		return $this->alliance;
	}

	public function getAPIData () {
		if(is_null($this->apiData))
			$this->apiData = $this->db->queryRow("SELECT * FROM ntCharacter WHERE id = :characterID", array(":characterID" => $this->characterID));
		return $this->apiData;
	}

	public function getItems ($ck = null) {
		if(is_null($this->items) || !is_null($ck)) {
			$items = array();
			$itemRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntItem.lastUpdateTimestamp FROM ntItem LEFT JOIN ntLocation ON ntItem.itemID = ntLocation.itemID WHERE ntItem.ownerID = :characterID", array(":characterID" => $this->characterID));
			foreach ($itemRows as $itemRow) {
				$item = new CoreItem($this->app, $itemRow);
				if(!is_null($ck) && !$ck($item))
					continue;
				array_push($items, $item);
			}
			if(is_null($ck))
				$this->items = $items;
			else
				return $items;
		}
		return $this->items;
	}

	public function getContainers ($ck = null) {
		if(is_null($this->containers) || !is_null($ck)) {
			$containers = array();
			$containerRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z,ntItem.lastUpdateTimestamp FROM ntItem,ntLocation WHERE ntItem.ownerID = :characterID AND ntLocation.itemID = ntItem.itemID", array(":characterID" => $this->characterID));
			foreach ($containerRows as $containerRow) {
				$container = new CoreContainer($this->app, $containerRow);
				if(!is_null($ck) && !$ck($container))
					continue;
				array_push($containers, $container);
			}
			if(is_null($ck))
				$this->containers = $containers;
			else
				return $containers;
		}
		return $this->containers;
	}

	public function getPermissions () {
		if(is_null($this->permissions)) {
			$this->permissions = array();
			$groups = $this->getCGroups();
			foreach($groups as $group)
				$this->permissions = array_merge($this->permissions, $group->getPermissions());
		}
		return $this->permissions;
	}

	public function getCPermissions () {
		if(is_null($this->cpermissions)) {
			$permissions = $this->getPermissions();
			$this->cpermissions = array();
			foreach ($permissions as $permission)
				array_push($this->cpermissions, $this->app->CoreManager->getPermission($permission));
		}
		return $this->cpermissions;
	}

	public function resetPermissions () {
		$this->permissions = null;
		$this->cpermissions = null;
	}

	public function hasPermission ($perm, $scope = null) {
		if($this->getCUser() && $this->getCUser()->isAdmin()) return true;
		if(is_int($perm)) {
			return in_array($perm, $this->getPermissions());
		} else if(is_string($perm)) {
			$permission = $this->app->CoreManager->getPermission($perm, $scope);
			return !(is_null($permission) || !in_array($permission->getId(), $this->getPermissions()));
		}
	}

	public function getGroups () {
		if(is_null($this->groups)) {
			$groupRows = $this->db->query("SELECT groupID FROM easGroupMembers WHERE characterID = :characterID", array(":characterID" => $this->characterID));
			$this->groups = array();
			foreach ($groupRows as $groupRow)
				array_push($this->groups, (int)$groupRow['groupID']);
		}
		return $this->groups;
	}

	public function getCGroups () {
		if(is_null($this->cgroups)) {
			$groups = $this->getGroups();
			$this->cgroups = array();
			foreach ($groups as $group)
				array_push($this->cgroups, $this->app->CoreManager->getGroup($group));
		}
		return $this->cgroups;
	}

	public function resetGroups () {
		$this->groups = null;
		$this->cgroups = null;
		$this->resetPermissions();
	}

	public function getFleets () {
		if(is_null($this->fleets)) {
			$fleetRows = $this->db->query("SELECT fleetID FROM easFleetParticipants WHERE characterID = :characterID", array(":characterID" => $this->characterID));
			$this->fleets = array();
			foreach ($fleetRows as $fleetRow)
				array_push($this->fleets, $fleetRow['fleetID']);
		}
		return $this->fleets;
	}

	public function getCFleets () {
		if(is_null($this->cfleets)) {
			$fleets = $this->getFleets();
			$this->cfleets = array();
			foreach ($fleets as $fleet)
				array_push($this->cfleets, $this->app->CoreManager->getFleet($fleet));
		}
		return $this->cfleets;
	}

	public function resetFleets () {
		$this->fleets = null;
		$this->cfleets = null;
	}

	public function getNotifications () {
		if(is_null($this->notifications)) {
			$permString = implode(",", $this->getPermissions());
			if($permString == "")
				$permString = "0";
			$corpNotifications = $this->db->query(
				"SELECT easNotifications.*, (SELECT 1 as i FROM easNotificationReaders WHERE notificationID = easNotifications.id AND readerID = :characterID LIMIT 0, 1) as readState
				FROM
					easNotifications
				LEFT JOIN easNotificationSettings
				ON
					easNotifications.recipientID = easNotificationSettings.corporationID,
					easNotificationTypes,
					easPermissions
				WHERE
					easNotifications.typeID = easNotificationTypes.typeID
				AND
					easNotificationTypes.permissionID IN ({$permString})
				AND
					easNotificationTypes.permissionID = easPermissions.id
				AND
					(
				        (
				            easNotificationSettings.scope = easPermissions.scope
				        )
				    OR
				        (
				            easNotificationSettings.scope IS NULL
				        AND
				            easPermissions.scope = 'corporation'
				        )
				    )
				AND
					(
						(
							(
				                easNotificationSettings.scope = 'corporation'
				            OR
				                easNotificationSettings.scope IS NULL
				            )
						AND
							easNotifications.recipientID = :corporationID
						)
					OR
						(
							easNotificationSettings.scope = 'alliance'
						AND
							:allianceID = (SELECT alliance FROM ntCorporation WHERE id = easNotifications.recipientID)
						)
					OR
						(
							easNotificationSettings.scope = 'blue'
						AND
							0 = 1
						)
					) ORDER BY easNotifications.requested DESC",
				array(
					":characterID" => $this->getId(),
					":corporationID" => $this->getCorpId(),
					":allianceID" => $this->getAlliId()
				)
			);
			$charNotifications = $this->db->query(
				"SELECT easNotifications.*, (SELECT 1 as i FROM easNotificationReaders WHERE notificationID = easNotifications.id AND readerID = :characterID LIMIT 0, 1) as readState
				FROM easNotifications
				WHERE recipientID = :characterID
				ORDER BY requested DESC",
				array(":characterID" => $this->getId()));
			$this->notifications = array_merge($corpNotifications, $charNotifications);
			usort($this->notifications, function ($a, $b) { return $a['requested'] < $b['requested']; });
		}
		return $this->notifications;
	}

	public function getCNotification ($notificationID) {
		$notifications = $this->getNotifications();
		foreach ($notifications as $notification) {
			if($notification['id'] == $notificationID) {
				return new CoreNotification($this->app, $notification);
			}
		}
		return null;
	}

	public function getCNotifications ($ck = null) {
		if(is_null($this->cnotifications) || !is_null($ck)) {
			$tnotifs = array();
			$notifications = $this->getNotifications();
			$this->cnotifications = array();
			foreach ($notifications as $notification) {
				$tnotif = new CoreNotification($this->app, $notification);
				if(!is_null($ck) && !$ck($tnotif))
					continue;
				array_push($tnotifs, $tnotif);
			}
			if(is_null($ck))
				$this->cnotifications = $tnotifs;
			else
				return $tnotifs;
		}
		return $this->cnotifications;
	}

	public function getOptions () {
		if(is_null($this->options)) {
			$this->options = $this->db->query("SELECT `key`, `value` FROM easCharacterOptions WHERE characterID = :characterID", array(":characterID" => $this->getId()));
		}
		return $this->options;
	}

	public function getOption ($key) {
		return $this->db->query("SELECT `key`, `value` FROM easCharacterOptions WHERE characterID = :characterID AND `key` = :key", array(":characterID" => $this->getId(), ":key" => $key));;
	}

	public function addOption ($key, $value) {
		$this->db->execute("INSERT INTO easCharacterOptions (characterID, `key`, `value`) VALUES (:characterID, :key, :value)", array(":characterID" => $this->getId(), ":key" => $key, ":value" => $value));
		$this->options = null;
	}

	public function setOption ($key, $value) {
		$this->delOption($key);
		$this->addOption($key, $value);
	}

	public function delOption ($key, $value = null) {
		if(is_null($value)) {
			$this->db->execute("DELETE FROM easCharacterOptions WHERE characterID = :characterID AND `key` = :key", array(":characterID" => $this->getId(), ":key" => $key));
		} else {
			$this->db->execute("DELETE FROM easCharacterOptions WHERE characterID = :characterID AND `key` = :key AND `value` = :value", array(":characterID" => $this->getId(), ":key" => $key, ":value" => $value));
		}
		$this->options = null;
	}

	public function setBaseGroups () {

        // remove old groups
        $oldgroups = $this->getCGroups();
        for($i = 0; $i < count($oldgroups); $i++)
            $oldgroups[$i]->removeCharacter($this->getId());

        $this->resetGroups();

        $corporation = $this->getCCorporation();
        $corporationGroup = $this->app->CoreManager->getGroup($this->getCorpName());
        if(is_null($corporationGroup))
            $corporationGroup = $this->app->CoreManager->createGroup($corporation->getName(), "corporation", $this->getCorpId(), 0);
        $corporationGroup->addCharacter($this->getId());

        if($corporation->getCeoCharacterId() == $this->getId())
            $this->app->CoreManager->getGroup("CEO")->addCharacter($this->getId());

        $alliance = $this->getCAlliance();
        if($alliance) {
			$allianceGroup = $this->app->CoreManager->getGroup($this->getAlliName());
	        if(is_null($allianceGroup))
	            $allianceGroup = $this->app->CoreManager->createGroup($alliance->getName(), "alliance", $this->getAlliId(), 0);
	        $allianceGroup->addCharacter($this->getId());

	        if($alliance->getExecCorp()->getCeoCharacterId() == $this->getId())
	            $this->app->CoreManager->getGroup("Alliance CEO")->addCharacter($this->getId());
		}
    }

	public function setBaseOptions () {
		$options = array(
			"jid" => $this->getStripCharName()."@".$this->config->getConfig("jabber", "urls")
		);
		foreach ($options as $key => $value) {
			$this->setOption($key, $value);
		}
	}

	public function getStripCharName () {
		return preg_replace(array("/ /", "/'/"), array("_", "."), strtolower($this->getName()));
	}

	public function setVerified ($verified) {
		$this->verified = $verified;
	}

	public function getVerified () {
		return $this->verified;
	}

	public function jsonSerialize() {
		$ret = array(
			"id"				=> $this->getDBId(),
			//"user"			=> $this->getUser(),
			"characterID"		=> $this->getId(),
			"characterName"		=> $this->getName(),
			"corporationID"		=> $this->getCorpId(),
			"corporationName"	=> $this->getCorpName(),
			"allianceID"		=> $this->getAlliId(),
			"allianceName"		=> $this->getAlliName()
		);

		if(!is_null($this->verified))
			$ret['verified'] = $this->getVerified();
		return $ret;
	}

	public function getStandings () {
		if(is_null($this->standings)) {
			$standingRows = $this->db->query("SELECT * FROM ntContactList WHERE ownerID IN (:characterID, :corporationID, :allianceID) AND standing <> 0.0",
                array(
                    ":characterID"    	=> $this->getId(),
                    ":corporationID"    => $this->getCorpId(),
                    ":allianceID"       => $this->getAlliId()
                )
            );
            foreach ($standingRows as $standingRow) {
                if(isset($this->standings[$standingRow['contactID']])) {
                    $this->standings[$standingRow['contactID']] = max($this->standings[$standingRow['contactID']], $standingRow['standing']);
                } else {
                    $this->standings[$standingRow['contactID']] = $standingRow['standing'];
                }
            }
		}
		return $this->standings;
	}

	// default

	public function getDBId () {
		return (int)$this->id;
	}

	public function getUser () {
		return (int)$this->user;
	}

	public function getCorpId () {
		return (int)$this->corporationID;
	}

	public function getCorpName () {
		return $this->corporationName;
	}

	public function getAlliId () {
		return (int)$this->allianceID;
	}

	public function getAlliName () {
		return $this->allianceName;
	}

	public function setUser ($user) {
		$this->user = $user;
		$this->db->execute("UPDATE easCharacters SET user = :user WHERE id = :id", array(":user" => $user, "id" => $this->getDBId()), true);
	}

	public function setCharId ($charId) {
		$this->characterID = $charId;
		$this->db->execute("UPDATE easCharacters SET characterID = :characterID WHERE id = :id", array(":characterID" => $charId, "id" => $this->getDBId()), true);
	}

	public function setCharName ($charName) {
		$this->characterName = $charName;
		$this->db->execute("UPDATE easCharacters SET characterName = :characterName WHERE id = :id", array(":characterName" => $charName, "id" => $this->getDBId()), true);
	}

	public function setCorpId ($corpId) {
		$this->corporationID = $corpId;
		$this->db->execute("UPDATE easCharacters SET corporationID = :corporationID WHERE id = :id", array(":corporationID" => $corpId, "id" => $this->getDBId()), true);
	}

	public function setCorpName ($corpName) {
		$this->corporationName = $corpName;
		$this->db->execute("UPDATE easCharacters SET corporationName = :corporationName WHERE id = :id", array(":corporationName" => $corpName, "id" => $this->getDBId()), true);
	}

	public function setAlliId ($alliId) {
		$this->allianceID = $alliId;
		$this->db->execute("UPDATE easCharacters SET allianceID = :allianceID WHERE id = :id", array(":allianceID" => $alliId, "id" => $this->getDBId()), true);
	}

	public function setAlliName ($alliName) {
		$this->allianceName = $alliName;
		$this->db->execute("UPDATE easCharacters SET allianceName = :allianceName WHERE id = :id", array(":allianceName" => $alliName, "id" => $this->getDBId()), true);
	}

}
