<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreCorporation extends CoreEntity {

	protected $id;
	protected $shortName;
	protected $name;
	protected $ceoCharacterID;
	protected $alliance;
	protected $npc;

	protected $ceoChar;
	protected $memberList;
	protected $fullMemberList;
	protected $alli;

	protected $items;
	protected $containers;
	protected $controltower;

	// custom

	public function getCEOChar () {
		if(is_null($this->ceoChar))
			$this->ceoChar = $this->app->CoreManager->getCharacter($this->ceoCharacterID);
		return $this->ceoChar;
	}

	public function getMemberList ($ck = null) {
		if(is_null($this->memberList) || !is_null($ck)) {
			$memberList = array();
			$memberRows = $this->db->query("SELECT * FROM easCharacters WHERE corporationID = :corporationID", array(":corporationID" => $this->id));
			foreach ($memberRows as $memberRow) {
				$character = new CoreCharacter($this->app, $memberRow);
				if(!is_null($ck))
					if(!$ck($character))
						continue;
				array_push($memberList, $character);
			}
			if(is_null($ck))
				$this->memberList = $memberList;
			else
				return $memberList;
		}
		return $this->memberList;
	}

	public function getFullMemberList ($ck = null, $showVerified = false) {
		if(is_null($this->fullMemberList) || !is_null($ck)) {
			$fullMemberList = array();
			$memberRows = $this->db->query("SELECT ntCharacter.id as characterID, ntCharacter.name as characterName, ntCharacter.corporation as corporationID, ntCorporation.name as corporationName, ntCorporation.alliance as allianceID, ntAlliance.name as allianceName FROM ntCharacter LEFT JOIN ntCorporation ON ntCharacter.corporation = ntCorporation.id LEFT JOIN ntAlliance ON ntCorporation.alliance = ntAlliance.id WHERE corporation = :corporationID", array(":corporationID" => $this->id));
			foreach ($memberRows as $memberRow) {
				$character = new CoreCharacter($this->app, $memberRow);
				if($showVerified)
					$character->setVerified(count($this->db->queryRow("SELECT * FROM ntAPIKeyCharacter WHERE characterID = :characterID", array(":characterID" => $character->getCharId()))) > 0 ? true : false);
				array_push($fullMemberList, $character);
			}
			if(is_null($ck))
				$this->fullMemberList = $fullMemberList;
			else
				return $fullMemberList;
		}
		return $this->fullMemberList;
	}

	public function getCAlliance () {
		if(is_null($this->alli))
			$this->alli = $this->app->CoreManager->getAlliance($this->alliance);
		return $this->alli;
	}

	public function getItems ($ck = null) {
		if(is_null($this->items) || !is_null($ck)) {
			$items = array();
			$itemRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntItem.lastUpdateTimestamp FROM ntItem LEFT JOIN ntLocation ON ntItem.itemID = ntLocation.itemID WHERE ntItem.ownerID = :corporationID", array(":corporationID" => $this->id));
			foreach ($itemRows as $itemRow) {
				$item = new CoreItem($this->app, $itemRow);
				if(!is_null($ck))
					if(!$ck($item))
						continue;
				array_push($items, $item);
			}
			if(is_null($ck))
				$this->items = $items;
			else
				return $items;
		}
		return $this->items;
	}

	public function getContainers ($ck = null) {
		if(is_null($this->containers) || !is_null($ck)) {
			$containers = array();
			$containerRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z,ntItem.lastUpdateTimestamp FROM ntItem,ntLocation WHERE ntItem.ownerID = :corporationID AND ntLocation.itemID = ntItem.itemID", array(":corporationID" => $this->id));
			foreach ($containerRows as $containerRow) {
				$container = new CoreContainer($this->app, $containerRow);
				if(!is_null($ck))
					if(!$ck($container))
						continue;
				array_push($containers, $container);
			}
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
			$controltowerRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z,ntItemStarbase.state,ntItemStarbase.moonID,ntItem.lastUpdateTimestamp FROM ntItem,ntLocation,ntItemStarbase,mapSolarSystems,mapRegions WHERE ntItem.ownerID = :corporationID AND ntLocation.itemID = ntItem.itemID AND ntItemStarbase.itemID = ntItem.itemID AND mapSolarSystems.solarSystemID=ntItem.locationID AND mapRegions.regionID=mapSolarSystems.regionID ORDER BY mapRegions.regionName ASC, mapSolarSystems.solarSystemName ASC, ntLocation.name ASC", array(":corporationID" => $this->id));
			foreach ($controltowerRows as $controltowerRow) {
				$controltower = new CoreControltower($this->app, $controltowerRow);
				if(!is_null($ck))
					if(!$ck($controltower))
						continue;
				array_push($controltowers, $controltower);
			}
			if(is_null($ck))
				$this->controltower = $controltowers;
			else
				return $controltowers;
		}
		return $this->controltower;
	}

	public function getStandings () {
		if(is_null($this->standings)) {
			$standingRows = $this->db->query("SELECT * FROM ntContactList WHERE ownerID IN (:corporationID, :allianceID) AND standing <> 0.0", 
                array(
                    ":corporationID"    => $this->getId(), 
                    ":allianceID"       => $this->getAlliance()
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

	public function isNPC () {
		return $this->getId() <= 1000259;
	}

	// default

	public function getShortName () {
		return $this->shortName;
	}

	public function getCeoCharacterId () {
		return (int)$this->ceoCharacterID;
	}

	public function getAlliance () {
		return (int)$this->alliance;
	}

	public function getNpc () {
		return $this->npc;
	}

}
