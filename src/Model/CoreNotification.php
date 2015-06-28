<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreNotification extends CoreBase {

	protected $id;
	protected $eveID;
	protected $state;
	protected $typeID;
	protected $creatorID;
	protected $recipientID;
	protected $locationID;
	protected $body;
	protected $created;
	protected $requested;
	protected $readState;

	protected $bodyData;
	protected $type;
	protected $scope;

	// custom

	public function getBodyData () {
		if(is_null($this->bodyData)) {
			$this->bodyData = @json_decode($this->body, true);
		}
		return $this->bodyData;
	}

	public function getScope () {
		if(is_null($this->scope)) {
			$this->scope = $this->db->queryField("SELECT scope FROM easNotificationSettings WHERE corporationID = :recipientID", "scope", array(":recipientID" => $this->recipientID));
			if(is_null($this->scope))
				$this->scope = "corporation";
		}
		return $this->scope;
	}

	public function getType () {
		if(is_null($this->type)) {
			$this->type = $this->db->queryRow("SELECT easNotificationTypes.* FROM easNotificationTypes, easPermissions WHERE easNotificationTypes.typeID = :typeID AND easNotificationTypes.permissionID = easPermissions.id AND easPermissions.scope = :scope", array(":typeID" => $this->typeID, ":scope" => $this->getScope()));
		}
		return $this->type;
	}

	// default

	public function getId () {
		return $this->id;
	}

	public function getEveId () {
		return $this->eveID;
	}

	public function getState () {
		return $this->state;
	}

	public function getTypeId () {
		return $this->typeID;
	}

	public function getCreatorId () {
		return $this->creatorID;
	}

	public function getRecipientId () {
		return $this->recipientID;
	}

	public function getLocationId () {
		return $this->locationID;
	}

	public function getBody () {
		return $this->body;
	}

	public function getCreated () {
		return $this->created;
	}

	public function getRequested () {
		return $this->requested;
	}

	public function getReadState () {
		return $this->readState;
	}

}
