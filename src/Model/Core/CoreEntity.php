<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreEntity extends CoreBase {

	protected $id;
	protected $name;

	protected $standings;

	public function getId () {
		return $this->getType() == "character" ? (int)$this->characterID : (int)$this->id;
	}

	public function getName () {
		return $this->getType() == "character" ? $this->characterName : $this->name;
	}

	public function derivedStanding ($char) {
		$standings = $this->getStandings();
		$relationships = [];
        if(isset($this->standings[$char->getId()])) $relationships[] = $this->standings[$char->getId()];
        if(isset($this->standings[$char->getCorpId()])) $relationships[] = $this->standings[$char->getCorpId()];
        if($char->getAlliId() != 0 && isset($this->standings[$char->getAlliId()])) $relationships[] = $this->standings[$char->getAlliId()];

        return count($relationships) > 0 ? max($relationships) : 0.0;
	}

	public function getType () {
		switch (get_class($this)) {
			case 'ProjectRena\Model\Core\CoreCharacter':
				return "character";
			case 'ProjectRena\Model\Core\CoreCorporation':
				return "corporation";
			case 'ProjectRena\Model\Core\CoreAlliance':
				return "alliance";

			default:
				return null;
		}
	}

}
