<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreControltower extends CoreStructure {

	protected $state;
	protected $moonID;

	protected $moon;
	protected $resources;
	protected $modules;
	protected $reactions;
	protected $ccontent;

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

	public function getResources () {
		return $this->db->query("SELECT * FROM invControlTowerResources WHERE controlTowerTypeID = :controlTowerTypeID AND factionID IS NULL", array(":controlTowerTypeID" => $this->typeID));
	}

	public function jsonSerialize() {
		$id = $this->itemID;
		return array(
    		"id" 				=> (int)$this->getItemId(),
    		"name" 				=> $this->getName(),
    		"state" 			=> (int)$this->getState(),
    		"moonName" 			=> $this->getMoon()->getName(),
    		"typeID" 			=> (int)$this->getTypeId(),
    		"typeName" 			=> $this->getType()->getName(),
    		"capacity" 			=> (float)$this->getType()->getCapacity(),
    		"solarSystemID" 	=> (int)$this->getLocation()->getId(),
    		"solarSystemName" 	=> $this->getLocation()->getName(),
    		"regionName" 		=> $this->getLocation()->getLocation()->getName(),
    		"corpName" 			=> $this->getOwner()->getName(),
    		"corpID" 			=> (int)$this->getOwner()->getId(),
    		"sov" 				=> $this->getLocation()->getOwner() && $this->getOwner()->getCAlliance() && $this->getLocation()->getOwner()->getId() == $this->getOwner()->getCAlliance()->getId(),
    		"secondaryCapacity" => (float)$this->app->CoreManager->getDGMAttribute($this->getTypeId(), 1233)['valueFloat'],
    		"content" 			=> $this->getContent(),
			"reactions"			=> $this->db->query("SELECT source, destination FROM easControltowerReactions WHERE towerID = :towerID", array(":towerID" => $this->getItemId())),
			"modules"			=> $this->getModules(),
			"fuel"				=> $this->getFuelLevel(),
			"strontium"			=> $this->getStrontiumLevel(),
    		//"resources" 			=> $ct->getResources()
		);
	}

	public function getStateName () {
		$states = array("Unanchored", "Anchored / Offline", "Onlining", "Reinforced", "Online");
		return $states[$this->state];
	}

	public function getFuelLevel () {
		$id = $this->itemID;
		$content = $this->getContent();
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
		$content = $this->getContent();
		$stront = 0;
		foreach ($content as $item) {
			if($item->getTypeId() == 16275) {
				$stront += $item->getType()->getVolume() * $item->getQuantity();
			}
		}
		return $stront / $this->app->CoreManager->getDGMAttribute($this->getTypeId(), 1233)['valueFloat'] * 100;
	}

	public function getModules () {
		if(is_null($this->modules)) {
			$this->modules = array();
			$id = $this->getId();
			$loc = $this->getLocationId();
			$x = $this->getX();
			$y = $this->getY();
			$z = $this->getZ();
			$this->modules = $this->getOwner()->getContainers(function($i) use ($id, $loc, $x, $y, $z) { return $i->getId() != $id && $i->getLocationId() == $loc && !is_null($i->getX()) && $i->getX() != 0 && !is_null($i->getY()) && $i->getY() != 0 && !is_null($i->getZ()) && $i->getZ() != 0 && sqrt(pow($i->getX() - $x, 2) + pow($i->getY() - $y, 2) + pow($i->getZ() - $z, 2)) <= 250000; });
			$this->setReactions();
		}
		return $this->modules;
	}

	public function setReactions () {
		if(is_null($this->reactions)) {
			$modifier = (100 + (count($this->app->CoreManager->getDGMAttribute($this->getTypeId(), 757)) > 0 ? $this->app->CoreManager->getDGMAttribute($this->getTypeId(), 757)['valueFloat'] : 0)) / 100;

			if(is_null($this->modules))
				$this->getModules();
			foreach ($this->modules as &$module) {
				if(in_array($module->getType()->getMarketGroupId(), [483, 488, 490])) {
					if($module->getType()->getMarketGroupId() == 483) $module->setCargoMod($modifier);
					//array_push($this->reactions, $module);
				}
			}
		}
		//return $this->reactions;
	}

	public function getContent () {
		if(is_null($this->ccontent)) {
			$id = $this->getId();
			$this->ccontent = $this->getOwner()->getItems(function($i) use ($id) { return $i->getLocationId() == $id; });
		}
		return $this->ccontent;
	}

	// default

	public function getState () {
		return (int)$this->state;
	}

	public function getMoonId () {
		return (int)$this->moonID;
	}

}
