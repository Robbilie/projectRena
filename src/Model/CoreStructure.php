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
		return $this->x;
	}

	public function getY () {
		return $this->y;
	}

	public function getZ () {
		return $this->z;
	}

	public function getClosestOrbital () {
		return $this->closestOrbital;
	}

}