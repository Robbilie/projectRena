<?php
namespace ProjectRena\Model;

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

	public function addPermission ($id) {
		if(!in_array($id, $this->getPermissions())) {
			$this->db->execute("INSERT INTO easGroupPermissions (groupID, permissionID) VALUES (:groupID, :permissionID)", array(":groupID" => $this->id, ":permissionID" => $id));
			$this->permissions = null;
			$this->cpermissions = null;
		}
	}

	public function removePermission ($id) {
		if(in_array($id, $this->getPermissions())) {
			$this->db->execute("DELETE FROM easGroupPermissions WHERE permissionID = :permissionID AND groupID = :groupID", array(":permissionID" => $id, ":groupID" => $this->id));
			$this->permissions = null;
			$this->cpermissions = null;
		}
	}

	public function hasPermission ($permissionID) {
		return in_array($permissionID, $this->getPermissions());
	}

	public function isCustom () {
		return $this->custom == 1;
	}

	public function jsonSerialize() {
		return array(
			"id" => $this->id,
			"name" => $this->name,
			"scope" => $this->scope,
			"owner" => $this->owner,
			"custom" => $this->isCustom(),
			"permissions" => $this->getPermissions()
		);
	}

	// default

	public function getId () {
		return $this->id;
	}

	public function getName () {
		return $this->name;
	}

	public function getScope () {
		return $this->scope;
	}

	public function getOwner () {
		return $this->owner;
	}

	public function getCustom () {
		return $this->custom;
	}
}