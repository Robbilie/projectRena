<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CorePermission extends CoreBase {

	protected $id;
	protected $name;
	protected $scope;

	// default

	public function getId () {
		return (int)$this->id;
	}

	public function getName () {
		return $this->name;
	}

	public function getScope () {
		return $this->scope;
	}

}
