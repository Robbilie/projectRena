<?php
namespace ProjectRena\Model\Core;

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

	public function isRead () {
		return !is_null($this->readState);
	}

	public function jsonSerialize() {
		return array(
			"id"			=> (int)$this->id,
			"eveID"			=> (int)$this->eveID,
			"state"			=> (int)$this->state,
			"typeID"		=> (int)$this->typeID,
			"creatorID"		=> (int)$this->creatorID,
			"recipientID"	=> (int)$this->recipientID,
			"locationID"	=> (int)$this->locationID,
			"created"		=> (int)$this->created,
			"requested"		=> (int)$this->requested,
			"body"			=> $this->body,
			"readState"		=> $this->isRead()
		);
	}

	// default

	public function getId () {
		return (int)$this->id;
	}

	public function getEveId () {
		return (int)$this->eveID;
	}

	public function getState () {
		return (int)$this->state;
	}

	public function getTypeId () {
		return (int)$this->typeID;
	}

	public function getCreatorId () {
		return (int)$this->creatorID;
	}

	public function getRecipientId () {
		return (int)$this->recipientID;
	}

	public function getLocationId () {
		return (int)$this->locationID;
	}

	public function getBody () {
		return $this->body;
	}

	public function getCreated () {
		return (int)$this->created;
	}

	public function getRequested () {
		return (int)$this->requested;
	}

	public function getReadState () {
		return $this->readState;
	}

}
