<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreFleet extends CoreBase {

  protected $id;
  protected $name;
  protected $comment;
  protected $hash;
  protected $creator;
  protected $time;
  protected $expires;

  protected $ccreator;
  protected $fleetparticipants;
  protected $cfleetparticipants;

  // custom

  public function getCCreator () {
    if(is_null($this->ccreator)) {
      $this->ccreator = $this->app->CoreManager->getCharacter($this->creator);
    }
    return $this->ccreator;
  }

  public function getFleetParticipants () {
		if(is_null($this->$fleetparticipants)) {
			$fleetparticipantRows = $this->db->query("SELECT characterID,confirmed FROM easFleetParticipants WHERE fleetID = :fleetID", array(":fleetID" => $this->id));
			$this->$fleetparticipants = array();
			foreach ($fleetparticipantRows as $fleetparticipantRow)
				array_push($this->$fleetparticipants, array("characterID" => (int)$fleetparticipantRow['characterID'], "confirmed" => ((int)$fleetparticipantRow['confirmed'] == 1)));
		}
		return $this->$fleetparticipants;
  }

  public function getCFleetParticipants () {
		if(is_null($this->$cfleetparticipants)) {
			$fleetparticipants = $this->getFleetParticipants();
			$this->$cfleetparticipants = array();
			foreach ($fleetparticipants as $fleetparticipant)
				array_push($this->$cfleetparticipants, $this->app->CoreManager->getFleetParticipant($fleetparticipant));
		}
		return $this->$cfleetparticipants;
  }

  public function isExpired () {
    return (time() - $this->expires) > 0;
  }

  public function hasParticipant ($participantID) {
    $participants = $this->getFleetParticipants();
    foreach ($participants as $participant)
      if($participant['characterID'] == $participantID)
        return true;
    return false;
  }

  public function confirmParticipant ($characterID) {
    foreach ($this->cfleetparticipants as $fleetparticipant) {
      if($fleetparticipant->getCharId() == $characterID) {
        $this->db->execute("UPDATE easFleetParticipants SET confirmed = 1 WHERE fleetID = :fleetID AND characterID = :characterID", array(":fleetID" => $this->id, ":characterID" => $characterID));
        $fleetparticipant->setConfirmed(true);
      }
    }
  }

	public function jsonSerialize() {
		return array(
			"id" => $this->id,
      "name" => $this->name,
      "comment" => $this->comment,
      "creatorID" => $this->creator,
      "creatorName" => $this->getCCreator()->getCharName(),
      "participants" => $this->getCFleetParticipants()
		);
	}

  // default

  public function getId () {
    return $this->id;
  }

  public function getName () {
    return $this->name;
  }

  public function getComment () {
    return $this->comment;
  }

  public function getHash () {
    return $this->hash;
  }

  public function getCreator () {
    return $this->creator;
  }

  public function getTime () {
    return $this->time;
  }

  public function getExpires () {
    return $this->expires;
  }

}
