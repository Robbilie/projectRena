<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreEntity extends CoreBase {

	protected $standings;

	public function derivedStanding ($char) {
		$standings = $this->getStandings();
		$relationships = [];
        if(isset($this->standings[$char->getCharId()])) $relationships[] = $this->standings[$char->getCharId()];
        if(isset($this->standings[$char->getCorpId()])) $relationships[] = $this->standings[$char->getCorpId()];
        if($char->getAlliId() != 0 && isset($this->standings[$char->getAlliId()])) $relationships[] = $this->standings[$char->getAlliId()];

        return count($relationships) > 0 ? max($relationships) : 0.0;
	}

}