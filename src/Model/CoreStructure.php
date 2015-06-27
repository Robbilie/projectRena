<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreStructure extends CoreItem {

	protected $x;
	protected $y;
	protected $z;
	protected $closestOrbital;

	public function setClosestOrbital ($orbitalID) {
		$this->closestOrbital = $orbitalID;
	}

	public function getX () {
		return (int)$this->x;
	}

	public function getY () {
		return (int)$this->y;
	}

	public function getZ () {
		return (int)$this->z;
	}

	public function getClosestOrbital () {
		return (int)$this->closestOrbital;
	}

}
