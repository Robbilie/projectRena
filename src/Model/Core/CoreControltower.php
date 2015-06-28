<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreControltower extends CoreStructure {

	protected $state;
	protected $moonID;

	protected $moon;
	protected $ressources;

	function __construct (RenaApp $app, $controltowerData = array()) {
		CoreBase::__construct($app, $controltowerData);

		$this->setClosestOrbital($this->moonID);
	}

	// custom

	public function getMoon () {
		if(is_null($this->moon))
			$this->moon = new CoreLocation($this->app, $this->db->queryRow("SELECT invNames.itemID as id,invNames.itemName as name FROM invNames WHERE itemID = :moonID", array(":moonID" => $this->moonID)));
		return $this->moon;
	}

	public function getRessources () {
		return $this->db->query("SELECT * FROM invControlTowerResources WHERE controlTowerTypeID = :controlTowerTypeID", array(":controlTowerTypeID" => $this->typeID));
	}

	public function jsonSerialize() {
		$id = $this->itemID;
		return array(
        		"id" 				=> $this->getItemId(),
        		"name" 				=> $this->getName(),
        		"state" 			=> $this->getState(),
        		"moonName" 			=> $this->getMoon()->getName(),
        		"typeID" 			=> $this->getTypeId(),
        		"typeName" 			=> $this->getType()->getName(),
        		"capacity" 			=> $this->getType()->getCapacity(),
        		"solarSystemID" 	=> $this->getLocation()->getId(),
        		"solarSystemName" 	=> $this->getLocation()->getName(),
        		"regionName" 		=> $this->getLocation()->getLocation()->getName(),
        		"corpName" 			=> $this->getOwner()->getName(),
        		"corpID" 			=> $this->getOwner()->getId(),
        		"sov" 				=> $this->getLocation()->getOwner() && $this->getLocation()->getOwner()->getId() == $this->getOwner()->getCAlliance()->getId(),
        		"secondaryCapacity" => $this->app->CoreManager->getDGMAttribute($this->getTypeId(), 1233)['valueFloat'],
        		"content" 			=> $this->getOwner()->getItems(function($i) use ($id) { return $i->getLocationId() == $id; }),
        		//"ressources" 		=> $ct->getRessources()
    		);
	}

	public function getStateName () {
		$states = array("Unanchored", "Anchored / Offline", "Onlining", "Reinforced", "Online");
		return $states[$this->state];
	}

	public function getFuelLevel () {
		$id = $this->itemID;
		$content = $this->getOwner()->getItems(function($i) use ($id) { return $i->getLocationId() == $id; });
		$fuel = 0;
		foreach ($content as $item) {
			if($item->getType()->getGroupId() == 1136) {
				$fuel += $item->getType()->getVolume() * $item->getQuantity();
			}
		}
		return $fuel / $this->getType()->getCapacity() * 100;
	}

	public function getStrontiumLevel () {
		$id = $this->itemID;
		$content = $this->getOwner()->getItems(function($i) use ($id) { return $i->getLocationId() == $id; });
		$stront = 0;
		foreach ($content as $item) {
			if($item->getTypeId() == 16275) {
				$stront += $item->getType()->getVolume() * $item->getQuantity();
			}
		}
		return $stront / $this->app->CoreManager->getDGMAttribute($this->getTypeId(), 1233)['valueFloat'] * 100;
	}

	public function getModules () {
		$id = $this->getId();
		$loc = $this->getLocationId();
		$x = $this->getX();
		$y = $this->getY();
		$z = $this->getZ();
		$mods = $this->getOwner()->getContainers(function($i) use ($id, $loc, $x, $y, $z) { return $i->getId() != $id && $i->getLocationId() == $loc && !is_null($i->getX()) && $i->getX() != 0 && !is_null($i->getY()) && $i->getY() != 0 && !is_null($i->getZ()) && $i->getZ() != 0 && sqrt(pow($i->getX() - $x, 2) + pow($i->getY() - $y, 2) + pow($i->getZ() - $z, 2)) <= 250000; });
		return $mods;
	}

	// default

	public function getState () {
		return (int)$this->state;
	}

	public function getMoonId () {
		return (int)$this->moonID;
	}

}
