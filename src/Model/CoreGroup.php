<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreGroup extends CoreBase {

	protected $id;
	protected $name;
	protected $permissions;
	protected $scope;
	protected $owner;
	protected $custom;

	protected $cowner;
	protected $permissionList;

	// custom

	public function getPermissionList () {
		if(is_null($this->permissionList)) {
			$this->permissionList = array();
			$permissionRows = $this->db->query("SELECT * FROM easPermissions WHERE :permissions & POWER(2, id) = POWER(2, id)", array(":permissions" => $this->permissions));
			foreach ($permissionRows as $permissionRow) {
				array_push($this->permissionList, new CorePermission($this->app, $permissionRow));
			}
		}
		return $this->permissionList;
	}

	public function getBit () {
		return pow(2, $this->id);
	}

	public function hasPermission ($permissionID) {
		return ($this->permissions & $permissionID) == $permissionID;
	}

	public function removePermission ($permissionID) {
		if(($this->permissions & $permissionID) == $permissionID) {
			$this->db->execute("UPDATE easGroups SET permissions = permissions - :permission WHERE id = :id AND permissions & :permission = :permission", array(":id" => $this->id, ":permission" => $permissionID));
			$this->permissions -= $permissionID;
			$this->getPermissionList();
		}
	}

	public function addPermission ($permissionID) {
		$this->db->execute("UPDATE easGroups SET permissions = permissions | :permission WHERE id = :id", array(":id" => $this->id, ":permission" => $permissionID));
		$this->permissions |= $permissionID;
		$this->getPermissionList();
	}

	public function isCustom () {
		return $this->custom == 1;
	}

	// default

	public function getId () {
		return $this->id;
	}

	public function getName () {
		return $this->name;
	}

	public function getPermissions () {
		return $this->permissions;
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