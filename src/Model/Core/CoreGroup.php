<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreGroup extends CoreBase {

	protected $id;
	protected $name;
	protected $scope;
	protected $owner;
	protected $custom;

	protected $cowner;
	protected $permissions;
	protected $cpermissions;
	protected $characters;
	protected $ccharacters;

	// custom

	public function getPermissions () {
		if(is_null($this->permissions)) {
			$permissionRows = $this->db->query("SELECT permissionID FROM easGroupPermissions WHERE groupID = :groupID", array(":groupID" => $this->id));
			$this->permissions = array();
			foreach ($permissionRows as $permissionRows)
				array_push($this->permissions, (int)$permissionRows['permissionID']);
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

	public function addPermission ($permissionID) {
		if(!in_array($permissionID, $this->getPermissions())) {
			$this->db->execute("INSERT INTO easGroupPermissions (groupID, permissionID) VALUES (:groupID, :permissionID)", array(":groupID" => $this->id, ":permissionID" => $permissionID));
			$this->resetPermissions();
		}
	}

	public function removePermission ($permissionID) {
		if(in_array($permissionID, $this->getPermissions())) {
			$this->db->execute("DELETE FROM easGroupPermissions WHERE permissionID = :permissionID AND groupID = :groupID", array(":permissionID" => $permissionID, ":groupID" => $this->id));
			$this->resetPermissions();
		}
	}

	public function resetPermissions () {
		$this->permissions = null;
		$this->cpermissions = null;
	}

	public function hasPermission ($permissionID) {
		return in_array($permissionID, $this->getPermissions());
	}

	public function getCharacters () {
		if(is_null($this->characters)) {
			$characterRows = $this->db->query("SELECT characterID FROM easGroupMembers WHERE groupID = :groupID", array(":groupID" => $this->id));
			$this->characters = array();
			foreach ($characterRows as $characterRow)
				array_push($this->characters, (int)$characterRow['characterID']);
		}
		return $this->characters;
	}

	public function getCCharacters () {
		if(is_null($this->ccharacters)) {
			$characters = $this->getCharacters();
			$this->ccharacters = array();
			foreach ($characters as $character)
				array_push($this->ccharacters, $this->app->CoreManager->getCharacter($character));
		}
		return $this->ccharacters;
	}

	public function addCharacter ($characterID) {
		if(!in_array($characterID, $this->getCharacters())) {
			$this->db->execute("INSERT INTO easGroupMembers (groupID, characterID) VALUES (:groupID, :characterID)", array(":groupID" => $this->id, ":characterID" => $characterID));
			$this->resetCharacters();
			$character = $this->app->CoreManager->getCharacter($characterID);
			$character->resetGroups();
		}
	}

	public function removeCharacter ($characterID) {
		if(in_array($characterID, $this->getCharacters())) {
			$this->db->execute("DELETE FROM easGroupMembers WHERE characterID = :characterID AND groupID = :groupID", array(":characterID" => $characterID, ":groupID" => $this->id));
			$this->resetCharacters();
			$character = $this->app->CoreManager->getCharacter($characterID);
			$character->resetGroups();
		}
	}

	public function resetCharacters () {
		$this->characters = null;
		$this->ccharacters = null;
	}

	public function isCustom () {
		return $this->custom == 1;
	}

	public function jsonSerialize() {
		return array(
			"id"					=> (int)$this->id,
			"name"				=> $this->name,
			"scope"				=> $this->scope,
			"owner"				=> (int)$this->owner,
			"custom"			=> $this->isCustom(),
			"permissions" => $this->getPermissions()
		);
	}

	// default

	public function getId () {
		return (int)$this->id;
	}

	public function getName () {
		return $this->name;
	}

	public function getScope () {
		return $this->scope;
	}

	public function getOwner () {
		return (int)$this->owner;
	}

	public function getCustom () {
		return $this->custom;
	}
}
