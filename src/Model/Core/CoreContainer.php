<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreContainer extends CoreItem {

	protected $x;
	protected $y;
	protected $z;

	protected $closestOrbital;
	protected $contents;
	protected $fill;

	protected $cargomod = 1;

	// custom

	public function setCargoMod ($cargomod) {
		$this->cargomod = $cargomod;
	}

	public function setClosestOrbital ($orbitalID) {
		$this->closestOrbital = $orbitalID;
	}

	public function getClosestOrbital () {
		return (int)$this->closestOrbital;
	}

	public function getContents () {
		if(is_null($this->contents)) {
			$this->contents = array();
			$this->contents = $this->app->CoreManager->getItemsByLocation($this->getId());
		}
		return $this->contents;
	}

	public function getFill () {
		if(is_null($this->fill)) {
			$this->fill = 0;
			$volume = 0;

			$contents = $this->getContents();
			foreach ($contents as $content) {
				$volume += $content->getType()->getVolume() * $content->getQuantity();
			}

			$this->fill = $volume / ($this->getType()->getCapacity() * $this->cargomod) * 100;
		}
		return $this->fill;
	}

	public function jsonSerialize() {
		return array(
			"ownerID"			=> (int)$this->ownerID,
			"itemID"			=> (int)$this->itemID,
			"typeID"			=> (int)$this->typeID,
			"typeName"			=> $this->getType()->getName(),
			"locationID"		=> (int)$this->locationID,
			"quantity"			=> (int)$this->quantity,
			"flag"				=> (int)$this->flag,
			"name"				=> $this->name,
			"volume"			=> (float)$this->getType()->getVolume(),
			"group"				=> (int)$this->getType()->getGroupId(),
			"x"					=> (int)$this->x,
			"y"					=> (int)$this->y,
			"z"					=> (int)$this->z,
			"fill"				=> (float)$this->getFill()
		);
	}

	// default

	public function getX () {
		return (int)$this->x;
	}

	public function getY () {
		return (int)$this->y;
	}

	public function getZ () {
		return (int)$this->z;
	}

}
