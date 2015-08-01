<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreAlliance extends CoreEntity {

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
		if(is_null($this->items) || !is_null($ck)) {
			$items = array();
			$corps = $this->getCorpList();
			foreach ($corps as $corp)
				$items = array_merge($items, $corp->getItems($ck));
			if(is_null($ck))
				$this->items = $items;
			else
				return $items;
		}
		return $items;
	}

	public function getContainers ($ck = null) {
		if(is_null($this->containers) || !is_null($ck)) {
			$containers = array();
			$corps = $this->getCorpList();
			foreach ($corps as $corp)
				$containers = array_merge($containers, $corp->getContainers($ck));
			if(is_null($ck))
				$this->containers = $containers;
			else
				return $containers;
		}
		return $this->containers;
	}

	public function getControltower ($ck = null) {
		if(is_null($this->controltower) || !is_null($ck)) {
			$controltowers = array();
			$corps = $this->getCorpList();
			foreach ($corps as $corp)
				$controltowers = array_merge($controltowers, $corp->getControltower($ck));
			if(is_null($ck))
				$this->controltower = $controltowers;
			else
				return $controltowers;
		}
		return $this->controltower;
	}

	public function getMemberList ($ck = null) {
		if(is_null($this->memberList) || !is_null($ck)) {
			$memberList = array();
			$corps = $this->getCorpList();
			foreach ($corps as $corp)
				$memberList = array_merge($memberList, $corp->getMemberList($ck));
			if(is_null($ck))
				$this->memberList = $memberList;
			else
				return $memberList;
		}
		return $this->memberList;
	}

	public function getStandings () {
		if(is_null($this->standings)) {
			$standingRows = $this->db->query("SELECT * FROM ntContactList WHERE ownerID = :allianceID AND standing <> 0.0", 
                array(
                    ":allianceID"       => $this->getId()
                )
            );
            foreach ($standingRows as $standingRow) {
                if(isset($this->standings[$standingRow['contactID']])) {
                    $this->standings[$standingRow['contactID']] = max($this->standings[$standingRow['contactID']], $standingRow['standing']);
                } else {
                    $this->standings[$standingRow['contactID']] = $standingRow['standing'];
                }
            }
		}
		return $this->standings;
	}

	// default

	public function getId () {
		return (int)$this->id;
	}

	public function getShortName () {
		return $this->shortName;
	}

	public function getName () {
		return $this->name;
	}

	public function getExecutorCorp () {
		return (int)$this->executorCorp;
	}

	public function getStartDate () {
		return (int)$this->startDate;
	}

	public function getMemberCount () {
		return (int)$this->memberCount;
	}

}
