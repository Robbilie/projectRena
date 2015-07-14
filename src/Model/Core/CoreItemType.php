<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreItemType extends CoreBase {

	protected $typeID;
	protected $groupID;
	protected $typeName;
	protected $description;
	protected $mass;
	protected $volume;
	protected $capacity;
	protected $marketGroupID;

	// default

	public function getId () {
		return (int)$this->typeID;
	}

	public function getName () {
		return $this->typeName;
	}

	public function getCapacity () {
		return (float)$this->capacity;
	}

	public function getVolume () {
		return (float)$this->volume;
	}

	public function getGroupId () {
		return (int)$this->groupID;
	}

	public function getMarketGroupId () {
		return (int)$this->marketGroupID;
	}

}
