<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreManager {

	private $app;
	private $db;
	private $config;

	function __construct(RenaApp $app) {
		$this->app = $app;
		$this->db = $this->app->Db;
		$this->config = $this->app->baseConfig;
	}

	public function login ($characterID) {
		$char = $this->createCharacter($characterID);

        if(isset($_SESSION['characterID'])) {
            $char->setUser($this->createCharacter($_SESSION['characterID'])->getUser());
        } else {
            if($char->getUser() == null) {
                $user = $this->createUser();
                $char->setUser($user->getId());
            }
        }
	}

	public function createUser () {
		return new CoreUser($this->app, array("id" => $this->db->execute("INSERT INTO easUsers (admin) VALUES (0)", array(), true)));
	}

	public function createCharacter ($characterID) {
		$char;
		$apiChar = (new \ProjectRena\Model\EVEApi\EVE\CharacterAffiliation($this->app))->getData([$characterID])["result"]["characters"][0];
		$charRow = $this->db->queryRow("SELECT * FROM easCharacters WHERE characterID = :characterID", array(":characterID" => $characterID));
		if(!$charRow) {
			$this->db->execute("INSERT INTO easCharacters (characterID, characterName, corporationID, corporationName, allianceID, allianceName, groups) VALUE (:characterID, :characterName, :corporationID, :corporationName, :allianceID, :allianceName, 0)",
                array(":characterID"        => $apiChar['characterID'],
                      ":characterName"      => $apiChar['characterName'],
                      ":corporationID"      => $apiChar['corporationID'],
                      ":corporationName"    => $apiChar['corporationName'],
                      ":allianceID"      	=> $apiChar['allianceID'],
                      ":allianceName"    	=> $apiChar['allianceName'],
                ), true);
			$charRow = $this->db->queryRow("SELECT * FROM easCharacters WHERE characterID = :characterID", array(":characterID" => $characterID));
			$char = new CoreCharacter($this->app, $charRow);
			$this->setBaseGroups($char);
		} else {
			$changed = false;
			$vars = array('characterID' => 'CharId', 'characterName' => 'CharName', 'corporationID' => 'CorpId', 'corporationName' => 'CorpName', 'allianceID' => 'AlliId', 'allianceName' => 'AlliName');
			$char = new CoreCharacter($this->app, $charRow);
			$ch = $this->charChanged($char, $apiChar);
			if($ch || $char->getGroups() == 0) {
				$this->setBaseGroups($char);
			}
		}
		return $char;
	}

	protected $groups = array();
	public function getGroup ($grp) {
		foreach ($this->groups as $group) {
			if(is_int($grp)) {
				if($group->getBit() == $grp)
					return $group;
			} else if(is_string($grp)) {
				if($group->getName() == $grp)
					return $group;
			}
		}
		$groupRow = array();
		if(is_int($grp)) {
			$groupRow = $this->db->queryRow("SELECT * FROM easGroups WHERE id = :bi", array(":bi" => log($grp, 2)));
		} else if(is_string($grp)) {
			$groupRow = $this->db->queryRow("SELECT * FROM easGroups WHERE name = :bi", array(":bi" => $grp));
		}
		if($groupRow) {
			$group = new CoreGroup($this->app, $groupRow);
			array_push($this->groups, $group);
			return $group;
		}
		return null;
	}

	public function getGroupsByScope ($scope) {
		$groups = array();
		$groupRows = $this->db->query("SELECT id FROM easGroups WHERE scope = :scope", array(":scope" => $scope));
		foreach ($groupRows as $groupRow)
			array_push($groups, $this->getGroup(pow(2, $groupRow['id'])));
		return $groups;
	}

	public function getGroupsByOwner ($owner = null) {
		$groups = array();
		$groupRows = $this->db->query("SELECT id FROM easGroups WHERE owner = :owner OR owner IS NULL", array(":owner" => $owner));
		foreach ($groupRows as $groupRow)
			array_push($groups, $this->getGroup(pow(2, $groupRow['id'])));
		return $groups;
	}

	public function getGroupsByOwnerAndScope ($owner, $scope) {
		$groups = array();
		$groupRows = $this->db->query("SELECT id FROM easGroups WHERE (owner = :owner OR owner IS NULL) AND scope = :scope", array(":owner" => $owner, ":scope" => $scope));
		foreach ($groupRows as $groupRow)
			array_push($groups, $this->getGroup(pow(2, $groupRow['id'])));
		return $groups;
	}

	protected $permissions = array();
	public function getPermission ($perm) {
		foreach ($this->permissions as $permission) {
			if(is_int($perm)) {
				if($permission->getBit() == $perm)
					return $permission;
			} else if(is_string($perm)) {
				if($permission->getName() == $perm)
					return $permission;
			}
		}
		$permissionRow = array();
		if(is_int($perm)) {
			$permissionRow = $this->db->queryRow("SELECT * FROM easPermissions WHERE id = :bi", array(":bi" => log($perm, 2)));
		} else if(is_string($perm)) {
			$permissionRow = $this->db->queryRow("SELECT * FROM easPermissions WHERE name = :bi", array(":bi" => $perm));
		}
		if($permissionRow) {
			$permission = new CorePermission($this->app, $permissionRow);
			array_push($this->permissions, $permission);
			return $permission;
		}
		return null;
	}

	public function getPermissionsByScope ($scope) {
		$permissions = array();
		$permissionRows = $this->db->query("SELECT id FROM easPermissions WHERE scope = :scope", array(":scope" => $scope));
		foreach ($permissionRows as $permissionRow)
			array_push($permissions, $this->getPermission(pow(2, $permissionRow['id'])));
		return $permissions;
	}

	protected $users = array();
	public function getUser ($userID) {
		foreach ($this->users as $user)
			if($user->getId() == $userID)
				return $user;
		$userRow = $this->db->queryRow("SELECT * FROM easUsers WHERE id = :userID", array(":userID" => $userID));
		if($userRow) {
			$user = new CoreUser($this->app, $userRow);
			array_push($this->users, $user);
			return $user;
		}
		return null;
	}

	protected $chars = array();
	public function getCharacter ($characterID) {
		foreach ($this->chars as $char)
			if($char->getId() == $characterID)
				return $char;
		$charRow = $this->db->queryRow("SELECT * FROM easCharacters WHERE characterID = :characterID", array(":characterID" => $characterID));
		if($charRow) {
			$char = new CoreCharacter($this->app, $charRow);
			array_push($this->chars, $char);
			return $char;
		} else {
			$ntCharRow = $this->db->queryRow("SELECT ntCharacter.id as characterID, ntCharacter.name as characterName, ntCharacter.corporation as corporationID, ntCorporation.name as corporationName, ntCorporation.alliance as allianceID, ntAlliance.name as allianceName FROM ntCharacter LEFT JOIN ntCorporation ON ntCharacter.corporation = ntCorporation.id LEFT JOIN ntAlliance ON ntCorporation.alliance = ntAlliance.id WHERE ntCharacter.id = :characterID", array(":characterID" => $characterID));
			if($ntCharRow) {
				$char = new CoreCharacter($this->app, $ntCharRow);
				array_push($this->chars, $char);
				return $char;
			}
		}
		return null;
	}

	protected $corps = array();
	public function getCorporation ($corporationID) {
		foreach ($this->corps as $corp)
			if($corp->getId() == $corporationID)
				return $corp;
		$corporationRow = $this->db->queryRow("SELECT * FROM ntCorporation WHERE id = :corporationID", array(":corporationID" => $corporationID));
		if($corporationRow) {
			$corp = new CoreCorporation($this->app, $corporationRow);
			array_push($this->corps, $corp); 
			return $corp;
		}
		return null;
	}

	protected $allis = array();
	public function getAlliance ($allianceID) {
		foreach ($this->allis as $alli)
			if($alli->getId() == $allianceID)
				return $alli;
		$allianceRow = $this->db->queryRow("SELECT * FROM ntAlliance WHERE id = :allianceID", array(":allianceID" => $allianceID));
		if($allianceRow) {
			$alli = new CoreAlliance($this->app, $allianceRow);
			array_push($this->allis, $alli);
			return $alli;
		}
		return null;
	}

	protected $items = array();
	public function getItem ($itemID) {
		foreach ($this->items as $item)
			if($item->getId() == $itemID)
				return $item;
		$itemRow = $this->db->queryRow("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name FROM ntItem LEFT JOIN ntLocation ON ntItem.itemID = ntLocation.itemID WHERE ntItem.itemID = :itemID", array(":itemID" => $itemID));
		if($itemRow) {
			$item = new CoreItem($this->app, $itemRow);
			array_push($this->items, $item);
			return $item;
		}
		return null;
	}

	protected $itemtypes = array();
	public function getItemType ($itemTypeID) {
		foreach ($this->itemtypes as $itemtype)
			if($itemtype->getId() == $itemTypeID)
				return $itemtype;
		$itemTypeRow = $this->db->queryRow("SELECT * FROM invTypes WHERE typeID = :typeID", array(":typeID" => $itemTypeID));
		if($itemTypeRow) {
			$itemtype = new CoreItemType($this->app, $itemTypeRow);
			array_push($this->itemtypes, $itemtype);
			return $itemtype;
		}
		return null;
	}

	protected $locations = array();
	public function getLocation ($locationID) {
		foreach ($this->locations as $location)
			if($location->getId() == $locationID)
				return $location;
		return null;
	}

	public function addLocation ($newlocation) {
		$location;
		foreach ($this->locations as $location)
			if($location->getId() == $newlocation->getId())
				return;
		array_push($this->locations, $newlocation);
	}

	protected $containers = array();
	public function getContainer ($containerID) {
		foreach ($this->containers as $container)
			if($container->getId() == $containerID)
				return $container;
		$containerRow = $this->db->queryRow("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z FROM ntItem,ntLocation WHERE ntItem.itemID = :itemID AND ntLocation.itemID = ntItem.itemID", array(":itemID" => $containerID));
		if($containerRow)
			return new CoreContainer($this->app, $containerRow);
		return null;
	}

	protected $controltowers = array();
	public function getControltower ($towerID) {
		foreach ($this->controltowers as $controltower)
			if($controltower->getId() == $towerID)
				return $controltower;
		$controltowerRow = $this->db->queryRow("SELECT ntItem.ownerID,ntItem.itemID,ntItem.typeID,ntItem.locationID,ntItem.quantity,ntItem.flag,ntLocation.name,ntLocation.x,ntLocation.y,ntLocation.z,ntItemStarbase.state,ntItemStarbase.moonID FROM ntItem,ntLocation,ntItemStarbase WHERE ntItem.itemID = :itemID AND ntLocation.itemID = ntItem.itemID AND ntItemStarbase.itemID = ntItem.itemID", array(":itemID" => $towerID));
		if($controltowerRow)
			return new CoreControltower($this->app, $controltowerRow);
		return null;
	}

	public function charChanged ($char, $apiChar) {
		$changed = false;
		$vars = array('characterID' => 'CharId', 'characterName' => 'CharName', 'corporationID' => 'CorpId', 'corporationName' => 'CorpName', 'allianceID' => 'AlliId', 'allianceName' => 'AlliName');
		foreach ($vars as $var => $mName) {
			if($char->{'get'.$mName}() != $apiChar[$var]) {
				$char->{'set'.$mName}($apiChar[$var]);
				$changed = true;
			}
		}
		return $changed;
	}

	public function setBaseGroups ($char) {
		$groups = 0;
		$allianceRow = $this->db->queryRow("SELECT * FROM easGroups WHERE name = :name", array(":name" => $char->getAlliName()));
		if($allianceRow) {
			$allianceGroup = new CoreGroup($this->app, $allianceRow);
			$groups += $allianceGroup->getBit();
		} else {
			$allianceGroup = $this->createGroup($char->getAlliName(), "alliance", $char->getAlliId(), 0);
			$groups += $allianceGroup->getBit();
		}
		$corporationRow = $this->db->queryRow("SELECT * FROM easGroups WHERE name = :name", array(":name" => $char->getCorpName()));
		if($corporationRow) {
			$corporationGroup = new CoreGroup($this->app, $corporationRow);
			$groups += $corporationGroup->getBit();
		} else {
			$corporationGroup = $this->createGroup($char->getCorpName(), "corporation", $char->getCorpId(), 0);
			$groups += $corporationGroup->getBit();
		}
		$char->setGroups($groups);
	}

	public function createGroup ($groupName, $scope, $owner = null, $custom = 0) {
		$id = $this->db->execute("INSERT INTO easGroups (name, permissions, scope, owner, custom) VALUES (:name, 0, :scope, :owner, :custom)", 
			array(
				":name" => $groupName, 
				":scope" => $scope, 
				":owner" => $owner,
				":custom" => $custom), true);
		return new CoreGroup($this->app, $this->db->queryRow("SELECT * FROM easGroups WHERE id = :id", array(":id" => $id)));
	}

	public function getDGMAttribute ($typeID, $attributeID) {
		return $this->db->queryRow("SELECT * FROM dgmTypeAttributes WHERE typeID = :typeID AND attributeID = :attributeID", array(":typeID" => $typeID, ":attributeID" => $attributeID));
	}

	public function charHasGroupPrivs ($char, $group) {
		if(
		    (
		        $group->getScope() == "corporation" &&
		        $group->getOwner() == $char->getCorpId() &&
		        $char->getCCorporation()->getCeoCharacterId() == $char->getCharId()
		    ) ||
		    (
		        $group->getScope() == "alliance" &&
		        $group->getOwner() == $char->getAlliId() &&
		        $char->getCCorporation()->getCAlliance()->getExecCorp()->getCeoCharacterId() == $char->getCharId()
		    ) ||
		    (
		        $char->getCUser()->isAdmin()
		    )
		) {
			return true;
		}
		return false; 
	}

	public function charCanAddCharToGroup ($char, $otherchar, $group) {
		$entity = $group->getScope() == "corporation" ? $this->getCorporation($group->getOwner()) : $group->getScope() == "alliance" ? $this->getAlliance($group->getOwner()) : null;
		if(
			$this->charHasGroupPrivs($char, $group) &&
			(
				(
					$group->getScope() == "corporation" &&
		        	$char->getCorpId() == $otherchar->getCorpId()
	        	) ||
	        	(
	        		$group->getScope() == "alliance" &&
		        	$char->getAlliId() == $otherchar->getAlliId()
        		) ||
        		(
        			$group->getScope() == "admin" &&
        			$char->getCUser()->isAdmin()
    			)
			)/* &&
			(
				$entity == null ||
				(
					$entity->getId() != $group->getOwner()
				)
			)*/
		) {
			return true;
		}
		return false;
	}

	public function entityExists ($name) {
		$res = $this->db->queryRow("SELECT * FROM (SELECT id,name FROM ntCorporation UNION SELECT id,name FROM ntAlliance) c WHERE c.name = :name", array(":name" => $name));
		if($res)
			return true;
		return false;
	}

	public function getCharacterLocation ($characterID) {
		return $this->db->queryField("SELECT locationID FROM easTracker WHERE characterID = :characterID ORDER BY timestamp DESC LIMIT 1", "locationID", array(":characterID" => $characterID));
	}
	
}