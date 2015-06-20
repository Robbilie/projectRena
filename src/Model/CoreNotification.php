<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreNotification extends CoreBase {

	protected $id;
	protected $type;
	protected $issuer;
	protected $recipient;
	protected $location;
	protected $created;
	protected $requested;
	protected $finished;
	protected $state;
	protected $permissions;
	protected $body;

	protected $issuerobj;
	protected $recipientobj;
	protected $locationobj;
	protected $permissionslist;
	protected $parsedbody;

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

	public function getBodyObj () {
		if(is_null($this->parsedbody))
			$this->parsedbody = @json_decode($this->body);
		return $this->parsedbody;
	}

	// default

	public function getId () {
		return $this->id;
	}

	public function getType () {
		return $this->type;
	}

	public function getIssuer () {
		return $this->issuer;
	}

	public function getRecipient () {
		return $this->recipient;
	}

	public function getLocation () {
		return $this->location;
	}

	public function getCreated () {
		return $this->created;
	}

	public function getRequested () {
		return $this->requested;
	}

	public function getFinished () {
		return $this->finished;
	}

	public function getState () {
		return $this->state;
	}

	public function getPermissions () {
		return $this->permissions;
	}

	public function getBody () {
		return $this->body;
	}

}