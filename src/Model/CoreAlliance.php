<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreAlliance extends CoreBase {

	protected $id;
	protected $shortName;
	protected $name;
	protected $executorCorp;
	protected $startDate;
	protected $memberCount;

	protected $execCorp;
	protected $corpList;
	protected $memberList;
	protected $fullMemberList;

	protected $items;
	protected $containers;
	protected $controltower;

	// custom

	public function getExecCorp () {
		if(is_null($this->execCorp))
			$this->execCorp = $this->app->CoreManager->getCorporation($this->executorCorp);
		return $this->execCorp;
	}

	public function getCorpList () {
		if(is_null($this->corpList)) {
			$this->corpList = array();
			$corporationRows = $this->db->query("SELECT id FROM ntCorporation WHERE alliance = :alliance", array(":alliance" => $this->id));
			foreach ($corporationRows as $corporationRow)
				array_push($this->corpList, $this->app->CoreManager->getCorporation($corporationRow['id']));
		}
		return $this->corpList;
	}

	public function getItems ($ck = null) {
		if(is_null($this->items)) {
			$this->items = array();
			$corps = $this->getCorpList();
			foreach ($corps as $corp)
				$this->items = array_merge($this->items, $corp->getItems($ck));
		}
		return $this->items;
	}

	public function getContainers ($ck = null) {
		if(is_null($this->containers)) {
			$this->containers = array();
			$corps = $this->getCorpList();
			foreach ($corps as $corp)
				$this->containers = array_merge($this->containers, $corp->getContainers($ck));
		}
		return $this->containers;
	}

	public function getControltower ($ck = null) {
		if(is_null($this->controltower)) {
			$this->controltower = array();
			$corps = $this->getCorpList();
			foreach ($corps as $corp)
				$this->controltower = array_merge($this->controltower, $corp->getControltower($ck));
		}
		return $this->controltower;
	}

	public function getMemberList ($ck = null) {
		if(is_null($this->memberList)) {
			$this->memberList = array();
			$corps = $this->getCorpList();
			foreach ($corps as $corp) {
				$this->memberList = array_merge($this->memberList, $corp->getMemberList($ck));
			}
		}
		return $this->memberList;
	}

	public function hasStandingsTowards ($character) {
		$r = $this->db->queryField(
				"SELECT count(contactID) as cnt FROM ntContactList WHERE
						ownerID = :ownerID AND
						(
								contactID = :characterID OR
								contactID = :corporationID OR
								contactID = :allianceID
						) AND
						standing > 0",
				"cnt",
				array(
						":ownerID" => $this->getId(),
						":characterID" => $character->getCharId(),
						":corporationID" => $character->getCorpId(),
						":allianceID" => $character->getAlliId()
				)
		);
		if($r == 0 && $this->getId() != $character->getAlliId()) {
				return false;
		} else {
				return true;
		}
	}

	// default

	public function getId () {
		return $this->id;
	}

	public function getShortName () {
		return $this->shortName;
	}

	public function getName () {
		return $this->name;
	}

	public function getExecutorCorp () {
		return $this->executorCorp;
	}

	public function getStartDate () {
		return $this->startDate;
	}

	public function getMemberCount () {
		return $this->memberCount;
	}

}
