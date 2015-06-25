<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreFleetParticipant extends CoreCharacter {

  protected $confirmed;

  // custom

  public function setConfirmed ($confirmed) {
    $this->confirmed = $confirmed;
  }

	public function jsonSerialize() {
		return array(
			"characterID" => $this->getCharId(),
      "characterName" => $this->getCharName(),
      "corporationName" => $this->getCorpName(),
      "confirmed" => $this->getConfirmed()
		);
	}

  // default

  public function getConfirmed () {
    return $this->confirmed;
  }

}
