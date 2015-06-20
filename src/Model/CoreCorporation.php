<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreCorporation extends CoreBase {

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
		if(is_null($this->memberList)) {
			$this->memberList = array();
			$memberRows = $this->db->query("SELECT * FROM easCharacters WHERE corporationID = :corporationID", array(":corporationID" => $this->id));
			foreach ($memberRows as $memberRow) {
				$character = new CoreCharacter($this->app, $memberRow);
				if(!is_null($ck))
					if(!$ck($character))
						continue;
				array_push($this->memberList, $character);
			}
		}
		return $this->memberList;
	}

	public function getFullMemberList ($ck = null) {
		if(is_null($this->fullMemberList)) {
			$this->fullMemberList = array();
			$memberRows = $this->db->query("SELECT ntCharacter.id as characterID, ntCharacter.name as characterName, ntCharacter.corporation as corporationID, ntCorporation.name as corporationName, ntCorporation.alliance as allianceID, ntAlliance.name as allianceName FROM ntCharacter LEFT JOIN ntCorporation ON ntCharacter.corporation = ntCorporation.id LEFT JOIN ntAlliance ON ntCorporation.alliance = ntAlliance.id WHERE corporation = :corporationID", array(":corporationID" => $this->id));
			foreach ($memberRows as $memberRow)
				array_push($this->fullMemberList, new CoreCharacter($this->app, $memberRow));
		}
		return $this->fullMemberList;
	}

	public function getCAlliance () {
		if(is_null($this->alli))
			$this->alli = $this->app->CoreManager->getAlliance($this->alliance);
		return $this->alli;
	}

	public function getItems ($ck = null) {
		if(is_null($this->items)) {
			$this->items = array();
			$itemRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name FROM ntItem LEFT JOIN ntLocation ON ntItem.itemID = ntLocation.itemID WHERE ntItem.ownerID = :corporationID", array(":corporationID" => $this->id));
			foreach ($itemRows as $itemRow) {
				$item = new CoreItem($this->app, $itemRow);
				if(!is_null($ck))
					if(!$ck($item))
						continue;
				array_push($this->items, $item);
			}
		}
		return $this->items;
	}

	public function getcontainers ($ck = null) {
		if(is_null($this->containers)) {
			$this->containers = array();
			$containerRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z FROM ntItem,ntLocation WHERE ntItem.ownerID = :corporationID AND ntLocation.itemID = ntItem.itemID", array(":corporationID" => $this->id));
			foreach ($containerRows as $containerRow) {
				$container = new CoreContainer($this->app, $containerRow);
				if(!is_null($ck))
					if(!$ck($container))
						continue;
				array_push($this->containers, $container);
			}
		}
		return $this->containers;
	}

	public function getControltower ($ck = null) {
		if(is_null($this->controltower)) {
			$this->controltower = array();
			$controltowerRows = $this->db->query("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z,ntItemStarbase.state,ntItemStarbase.moonID FROM ntItem,ntLocation,ntItemStarbase,mapSolarSystems,mapRegions WHERE ntItem.ownerID = :corporationID AND ntLocation.itemID = ntItem.itemID AND ntItemStarbase.itemID = ntItem.itemID AND mapSolarSystems.solarSystemID=ntItem.locationID AND mapRegions.regionID=mapSolarSystems.regionID ORDER BY mapRegions.regionName ASC, mapSolarSystems.solarSystemName ASC, ntLocation.name ASC", array(":corporationID" => $this->id));
			foreach ($controltowerRows as $controltowerRow) {
				$controltower = new CoreControltower($this->app, $controltowerRow);
				if(!is_null($ck))
					if(!$ck($controltower))
						continue;
				array_push($this->controltower, $controltower);
			}
		}
		return $this->controltower;
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

	public function getCeoCharacterId () {
		return $this->ceoCharacterID;
	}

	public function getAlliance () {
		return $this->alliance;
	}

	public function getNpc () {
		return $this->npc;
	}

}