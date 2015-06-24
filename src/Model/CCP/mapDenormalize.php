<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class mapDenormalize
{
				/**
				 * @var RenaApp
				 */
				private $app;
				/**
				 * @var \ProjectRena\Lib\Db
				 */
				private $db;

				/**
				 * @param RenaApp $app
				 */
				function __construct(RenaApp $app)
				{
								$this->app = $app;
								$this->db = $this->app->Db;
				}

				/**
				 * @param $itemID
				 *
				 * @return array
				 */
				public function getAllByID($itemID)
				{
								return $this->db->queryRow("SELECT * FROM mapDenormalize WHERE itemID = :id", array(":id" => $itemID), 3600);
				}

				/**
				 * @param $itemName
				 *
				 * @return array
				 */
				public function getAllByName($itemName)
				{
								return $this->db->queryRow("SELECT * FROM mapDenormalize WHERE itemName = :name", array(":name" => $itemName), 3600);
				}

				/**
				 * @param $typeID
				 *
				 * @return array|bool
				 * @throws \Exception
				 */
				public function getAllByTypeID($typeID)
				{
								return $this->db->query("SELECT * FROM mapDenormalize WHERE typeID = :typeID", array(":typeID" => $typeID), 3600);
				}

				/**
				 * @param $groupID
				 *
				 * @return array|bool
				 * @throws \Exception
				 */
				public function getAllByGroupID($groupID)
				{
								return $this->db->query("SELECT * FROM mapDenormalize WHERE groupID = :groupID", array(":groupID" => $groupID), 3600);
				}

				/**
				 * @param $solarSystemID
				 *
				 * @return array|bool
				 * @throws \Exception
				 */
				public function getAllBySolarSystemID($solarSystemID)
				{
								return $this->db->query("SELECT * FROM mapDenormalize WHERE solarSystemID = :solarSystemID", array(":solarSystemID" => $solarSystemID), 3600);
				}

				/**
				 * @param $constellationID
				 *
				 * @return array|bool
				 * @throws \Exception
				 */
				public function getAllByConstellationID($constellationID)
				{
								return $this->db->query("SELECT * FROM mapDenormalize WHERE constellationID = :constellationID", array(":constellationID" => $constellationID), 3600);
				}

				/**
				 * @param $regionID
				 *
				 * @return array|bool
				 * @throws \Exception
				 */
				public function getAllByRegionID($regionID)
				{
								return $this->db->query("SELECT * FROM mapDenormalize WHERE regionID = :regionID", array(":regionID" => $regionID), 3600);
				}

				/**
				 * @param $orbitID
				 *
				 * @return array|bool
				 * @throws \Exception
				 */
				public function getAllByOrbitID($orbitID)
				{
								return $this->db->query("SELECT * FROM mapDenormalize WHERE orbitID = :orbitID", array(":orbitID" => $orbitID), 3600);
				}
}