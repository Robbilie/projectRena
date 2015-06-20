<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreLocation extends CoreBase {

	protected $id;
	protected $name;
	protected $ownerID;

	protected $owner;
	protected $location;

	public function getId () {
		return $this->id;
	}

	public function getName () {
		return $this->name;
	}

	public function getOwner () {
		if(is_null($this->owner)) {
			$character = $this->app->CoreManager->getCharacter($this->ownerID);
			if($character)
				$this->owner = $character;
		}
		if(is_null($this->owner)) {
			$corporation = $this->app->CoreManager->getCorporation($this->ownerID);
			if($corporation)
				$this->owner = $corporation;
		}
		if(is_null($this->owner)) {
			$alliance = $this->app->CoreManager->getAlliance($this->ownerID);
			if($alliance)
				$this->owner = $alliance;
		}
		return $this->owner;
	}

	public function getLocation () {
		$changed = false;
		/*if(is_null($this->location)) {
			$oldlocation = $this->app->CoreManager->getLocation($this->id);
			if($oldlocation)
				$this->location = $oldlocation;
		}*/
		if(is_null($this->location)) {
			$locationRow = $this->db->queryRow("SELECT mapRegions.regionID as id,mapRegions.regionName as name,null as ownerID FROM mapRegions INNER JOIN mapSolarSystems ON mapSolarSystems.regionID = mapRegions.regionID WHERE mapSolarSystems.solarSystemID = :solarSystemID", array(":solarSystemID" => $this->id));
			if($locationRow)
				$this->location = new CoreLocation($this->app, array("id" => $locationRow['id'], "name" => $locationRow['name'], "ownerID" => $locationRow['ownerID']));
		}
		if($changed)
			$this->app->CoreManager->addLocation($this->location);
		return $this->location;
	}

}