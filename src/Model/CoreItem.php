<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreItem extends CoreBase {

	protected $ownerID;
	protected $itemID;
	protected $typeID;
	protected $locationID;
	protected $quantity;
	protected $flag;
	protected $name;

	protected $type;
	protected $owner;
	protected $location;

	// custom

	public function getType () {
		if(is_null($this->type))
			$this->type = $this->app->CoreManager->getItemType($this->typeID);
		return $this->type;
	}

	public function getOwner () {
		if(is_null($this->owner))
			$this->owner = $this->app->CoreManager->getCorporation($this->ownerID);
		if(is_null($this->owner))
			$this->owner = $this->app->CoreManager->getCharacter($this->ownerID);
		return $this->owner;
	}

	public function getLocation () {
		$changed = false;
		if(is_null($this->location)) {
			$oldlocation = $this->app->CoreManager->getLocation($this->locationID);
			if($oldlocation)
				$this->location = $oldlocation;
		}
		if(is_null($this->location)) {
			$controltower = $this->app->CoreManager->getControlTower($this->locationID);
			if($controltower) {
				$changed = true;
				$this->location = new CoreLocation($this->app, array("id" => $controltower->getItemId(), "name" => $controltower->getName(), "ownerID" => $controltower->getOwner()->getId(), "owner" => $controltower->getOwner()));
			}
		}
		if(is_null($this->location)) {
			$container = $this->app->CoreManager->getContainer($this->locationID);
			if($container) {
				$changed = true;
				$this->location = new CoreLocation($this->app, array("id" => $container->getItemId(), "name" => $container->getName(), "ownerID" => $container->getOwner()->getId(), "owner" => $container->getOwner()));
			}
		}
		if(is_null($this->location)) {
			$item = $this->app->CoreManager->getItem($this->locationID);
			if($item) {
				$changed = true;
				$this->location = new CoreLocation($this->app, array("id" => $item->getItemId(), "name" => $item->getName(), "ownerID" => $item->getOwner()->getId(), "owner" => $item->getOwner()));
			}
		}
		if(is_null($this->location)) {
			$locationRow = $this->db->queryRow("SELECT mapSolarSystems.solarSystemID as id,mapSolarSystems.solarSystemName as name,ntSovereignty.corporation as ownerID FROM mapSolarSystems INNER JOIN ntSovereignty ON mapSolarSystems.solarSystemID = ntSovereignty.solarSystem WHERE solarSystemID = :solarSystemID", array(":solarSystemID" => $this->locationID));
			if($locationRow) {
				$changed = true;
				$this->location = new CoreLocation($this->app, array("id" => $locationRow['id'], "name" => $locationRow['name'], "ownerID" => $locationRow['ownerID']));
			}
		}
		if(is_null($this->location)) {
			$locationRow = $this->db->queryRow("SELECT stationID as id,stationName as name,corporationID as ownerID FROM ntOutpost WHERE stationID = :stationID", array(":stationID" => $this->locationID));
			if($locationRow) {
				$changed = true;
				$this->location = new CoreLocation($this->app, array("id" => $locationRow['id'], "name" => $locationRow['name'], "ownerID" => $locationRow['ownerID']));
			}
		}
		if(is_null($this->location)) {
			$locationRow = $this->db->queryRow("SELECT itemID as id,itemName as name,null as ownerID FROM mapDenormalize WHERE itemID = :itemID", array(":itemID" => $this->locationID));
			if($locationRow) {
				$changed = true;
				$this->location = new CoreLocation($this->app, array("id" => $locationRow['id'], "name" => $locationRow['name'], "ownerID" => $locationRow['ownerID']));
			}
		}
		if($changed)
			$this->app->CoreManager->addLocation($this->location);
		return $this->location;
	}

	public function jsonSerialize() {
		return array(
				"ownerID"		=> $this->ownerID,
				"itemID"		=> $this->itemID,
				"typeID"		=> $this->typeID,
				"typeName"		=> $this->getType()->getName(),
				"locationID"	=> $this->locationID,
				"quantity"		=> $this->quantity,
				"flag"			=> $this->flag,
				"name"			=> $this->name,
				"volume"		=> $this->getType()->getVolume(),
				"group"			=> $this->getType()->getGroupId(),
				//{"ownerID":"98381268","itemID":"1016883797926","typeID":"4246","locationID":"1016815860349","quantity":"3420","flag":"0","name":null}
			);
	}

	// default

	public function getOwnerId () {
		return $this->ownerID;
	}

	public function getId () {
		return $this->itemID;
	}

	public function getItemId () {
		return $this->itemID;
	}

	public function getTypeId () {
		return $this->typeID;
	}

	public function getLocationId () {
		return $this->locationID;
	}

	public function getQuantity () {
		return $this->quantity;
	}

	public function getFlag () {
		return $this->flag;
	}

	public function getName () {
		return $this->name;
	}

}	