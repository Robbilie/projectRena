<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreCharacter extends CoreBase {

	protected $id;
	protected $user;
	protected $characterID;
	protected $characterName;
	protected $corporationID;
	protected $corporationName;
	protected $allianceID;
	protected $allianceName;
	protected $groups;

	protected $userObj;
	protected $corp;
	protected $groupList;

	protected $items;
	protected $containers;

	protected $permissions;

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

	public function getGroupList () {
		if(is_null($this->groupList)) {
			$this->groupList = array();
			$groupRows = $this->db->query("SELECT * FROM easGroups WHERE :groups & POWER(2, id) = POWER(2, id)", array(":groups" => $this->groups));
			foreach ($groupRows as $groupRow)
				array_push($this->groupList, new CoreGroup($this->app, $groupRow));
		}
		return $this->groupList;
	}

	public function getAPIData () {
		if(is_null($this->apiData))
			$this->apiData = $this->db->queryRow("SELECT * FROM ntCharacter WHERE id = :characterID", array(":characterID" => $this->characterID));
		return $this->apiData;
	}

	public function getItems ($ck = null) {
		if(is_null($this->items)) {
			$this->items = array();
			$itemRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name FROM ntItem LEFT JOIN ntLocation ON ntItem.itemID = ntLocation.itemID WHERE ntItem.ownerID = :characterID", array(":characterID" => $this->characterID));
			foreach ($itemRows as $itemRow) {
				$item = new CoreItem($this->app, $itemRow);
				if(!is_null($ck))
					if(!$ck($item))
						continue;
				array_push($this->items, $item);
			}
		}
		return $this->items;
	}

	public function getContainers ($ck = null) {
		if(is_null($this->containers)) {
			$this->containers = array();
			$containerRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z FROM ntItem,ntLocation WHERE ntItem.ownerID = :characterID AND ntLocation.itemID = ntItem.itemID", array(":characterID" => $this->characterID));
			foreach ($containerRows as $containerRow) {
				$container = new CoreContainer($this->app, $containerRow);
				if(!is_null($ck))
					if(!$ck($container))
						continue;
				array_push($this->containers, $container);
			}
		}
		return $this->containers;
	}

	public function getPermissions () {
		if(is_null($this->permissions)) {
			$perms = 0;
			$groups = $this->getGroupList();
			foreach ($groups as $group) {
				$perms |= $group->getPermissions();
			}
			$this->permissions = $perms;
		}
		return $this->permissions;
	}

	public function hasPermission ($perm) {
		if(is_int($perm)) {
			return ($this->getPermissions() & $perm) == $perm;
		} else if(is_string($perm)) {
			$permission = $this->app->CoreManager->getPermission($perm);
			return ($this->getPermissions() & $permission->getBit()) == $permission->getBit();
		}
	}

	// default

	public function getId () {
		return $this->id;
	}

	public function getUser () {
		return $this->user;
	}

	public function getCharId () {
		return $this->characterID;
	}

	public function getCharName () {
		return $this->characterName;
	}

	public function getCorpId () {
		return $this->corporationID;
	}

	public function getCorpName () {
		return $this->corporationName;
	}

	public function getAlliId () {
		return $this->allianceID;
	}

	public function getAlliName () {
		return $this->allianceName;
	}

	public function getGroups () {
		return $this->groups;
	}

	public function setUser ($user) {
		$this->user = $user;
		$this->db->execute("UPDATE easCharacters SET user = :user WHERE id = :id", array(":user" => $user, "id" => $this->getId()), true);
	}

	public function setCharId ($charId) {
		$this->characterID = $charId;
		$this->db->execute("UPDATE easCharacters SET characterID = :characterID WHERE id = :id", array(":characterID" => $charId, "id" => $this->getId()), true);
	}

	public function setCharName ($charName) {
		$this->characterName = $charName;
		$this->db->execute("UPDATE easCharacters SET characterName = :characterName WHERE id = :id", array(":characterName" => $charName, "id" => $this->getId()), true);
	}

	public function setCorpId ($corpId) {
		$this->corporationID = $corpId;
		$this->db->execute("UPDATE easCharacters SET corporationID = :corporationID WHERE id = :id", array(":corporationID" => $corpId, "id" => $this->getId()), true);
	}

	public function setCorpName ($corpName) {
		$this->corporationName = $corpName;
		$this->db->execute("UPDATE easCharacters SET corporationName = :corporationName WHERE id = :id", array(":corporationName" => $corpName, "id" => $this->getId()), true);
	}

	public function setAlliId ($alliId) {
		$this->allianceID = $alliId;
		$this->db->execute("UPDATE easCharacters SET allianceID = :allianceID WHERE id = :id", array(":allianceID" => $alliId, "id" => $this->getId()), true);
	}

	public function setAlliName ($alliName) {
		$this->allianceName = $alliName;
		$this->db->execute("UPDATE easCharacters SET allianceName = :allianceName WHERE id = :id", array(":allianceName" => $alliName, "id" => $this->getId()), true);
	}

	public function setGroups ($groups) {
		$this->groups = $groups;
		$this->db->execute("UPDATE easCharacters SET groups = :groups WHERE id = :id", array(":groups" => $groups, "id" => $this->getId()), true);
	}

}