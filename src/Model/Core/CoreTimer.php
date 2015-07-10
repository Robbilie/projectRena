<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreTimer extends CoreBase {

  protected $id;
  protected $scope;
  protected $creatorID;
  protected $ownerID;
  protected $typeID;
  protected $locationID;
  protected $rf;
  protected $comment;
  protected $timestamp;

  protected $ccreator;
  protected $cowner;
  protected $clocation;
  protected $citemtype;

  // custom

  public function getCCreator () {
    if(is_null($this->ccreator)) {
      $this->ccreator = $this->app->CoreManager->getCorporation($this->creatorID);
    }
    return $this->ccreator;
  }

  public function getCOwner () {
    if(is_null($this->cowner)) {
      $this->cowner = $this->app->CoreManager->getCorporation($this->ownerID);
      if(is_null($this->cowner))
        $this->cowner = $this->app->CoreManager->getAlliance($this->ownerID);
    }
    return $this->cowner;
  }

  public function getCLocation () {
    if(is_null($this->clocation)) {
      $this->clocation = $this->app->CoreManager->getCLocation($this->locationID, true);
    }
    return $this->clocation;
  }

  public function getCItemType () {
    if(is_null($this->citemtype)) {
      $this->citemtype = $this->app->CoreManager->getItemType($this->typeID);
    }
    return $this->citemtype;
  }

	public function jsonSerialize() {
		return array(
      "id" => (int)$this->id,
      "scope" => $this->scope,
      "creatorID" => (int)$this->id,
      "ownerID" => (int)$this->ownerID,
      "typeID" => (int)$this->typeID,
      "typeName" => $this->app->CoreManager->getItemType($this->typeID)->getName(),
      "locationID" => (int)$this->locationID,
      "locationName" => $this->getCLocation()->getName(),
      "rf" => (int)$this->rf,
			"comment" => $this->comment,
      "timestamp" => (int)$this->timestamp
		);
	}

  // default

  public function getId () {
    return $this->id;
  }

  public function getScope () {
    return $this->scope;
  }

  public function getCreatorId () {
    return $this->creatorID;
  }

  public function getOwnerId () {
    return $this->ownerID;
  }

  public function getTypeId () {
    return $this->typeID;
  }

  public function getLocationId () {
    return $this->locationID;
  }

  public function getRf () {
    return $this->rf;
  }

  public function getComment () {
    return $this->comment;
  }

  public function getTimestamp () {
    return $this->timestamp;
  }

}
