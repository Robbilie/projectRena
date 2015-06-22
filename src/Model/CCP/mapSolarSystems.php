<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class mapSolarSystems
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
				 * @param $solarSystemID
				 *
				 * @return array
				 */
				public function getAllByID($solarSystemID)
				{
								return $this->db->queryRow("SELECT * FROM mapSolarSystems WHERE solarSystemID = :id", array(":id" => $solarSystemID), 3600);
				}

				/**
				 * @param $solarSystemName
				 *
				 * @return array
				 */
				public function getAllByName($solarSystemName)
				{
								return $this->db->queryRow("SELECT * FROM mapSolarSystems WHERE solarSystemName = :name", array(":name" => $solarSystemName), 3600);
				}

				/**
				 * @param $solarSystemID
				 *
				 * @return null
				 */
				public function getNameByID($solarSystemID)
				{
								return $this->db->queryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $solarSystemID), 3600);
				}

				/**
				 * @param $solarSystemName
				 *
				 * @return null
				 */
				public function getIDByName($solarSystemName)
				{
								return $this->db->queryField("SELECT solarSystemID FROM mapSolarSystems WHERE solarSystemName = :name", "solarSystemID", array(":name" => $solarSystemName), 3600);
				}

				/**
				 * @param $solarSystemID
				 *
				 * @return null
				 */
				public function getRegionIDByID($solarSystemID)
				{
								return $this->db->queryField("SELECT regionID FROM mapSolarSystems WHERE solarSystemID = :id", "regionID", array(":id" => $solarSystemID), 3600);
				}

				/**
				 * @param $solarSystemName
				 *
				 * @return null
				 */
				public function getRegionIDByName($solarSystemName)
				{
								return $this->db->queryField("SELECT regionID FROM mapSolarSystems WHERE solarSystemName = :name", "regionID", array(":name" => $solarSystemName), 3600);
				}

				/**
				 * @param $solarSystemID
				 *
				 * @return null
				 */
				public function getConstellationIDByID($solarSystemID)
				{
								return $this->db->queryField("SELECT constellationID FROM mapSolarSystems WHERE solarSystemID = :id", "constellationID", array(":id" => $solarSystemID), 3600);
				}

				/**
				 * @param $solarSystemName
				 *
				 * @return null
				 */
				public function getConstellationIDByName($solarSystemName)
				{
								return $this->db->queryField("SELECT constellationID FROM mapSolarSystems WHERE solarSystemName = :name", "constellationID", array(":name" => $solarSystemName), 3600);
				}

				/**
				 * @param $solarSystemID
				 *
				 * @return null
				 */
				public function getCoordinatesByID($solarSystemID)
				{
								return $this->db->queryRow("SELECT x, y, z, xMin, xMax, yMin, yMax, zMin, zMax, luminosity, border, fringe, corridor, hub, international, regional, constellation FROM mapSolarSystems WHERE solarSystemID = :id", array(":id" => $solarSystemID), 3600);
				}

				/**
				 * @param $solarSystemName
				 *
				 * @return null
				 */
				public function getCoordinatesByName($solarSystemName)
				{
								return $this->db->queryRow("SELECT x, y, z, xMin, xMax, yMin, yMax, zMin, zMax, luminosity, border, fringe, corridor, hub, international, regional, constellation FROM mapSolarSystems WHERE solarSystemName = :name", array(":name" => $solarSystemName), 3600);
				}

				/**
				 * @param $solarSystemID
				 *
				 * @return null
				 */
				public function getSecurityByID($solarSystemID)
				{
								return $this->db->queryField("SELECT security FROM mapSolarSystems WHERE solarSystemID = :id", "security", array(":id" => $solarSystemID), 3600);
				}

				/**
				 * @param $solarSystemName
				 *
				 * @return null
				 */
				public function getSecurityByName($solarSystemName)
				{
								return $this->db->queryField("SELECT security FROM mapSolarSystems WHERE solarSystemName = :name", "security", array(":name" => $solarSystemName), 3600);
				}

				/**
				 * @param $solarSystemID
				 *
				 * @return null
				 */
				public function getSecurityClassByID($solarSystemID)
				{
								return $this->db->queryField("SELECT securityClass FROM mapSolarSystems WHERE solarSystemID = :id", "securityClass", array(":id" => $solarSystemID), 3600);
				}

				/**
				 * @param $solarSystemName
				 *
				 * @return null
				 */
				public function getSecurityClassByName($solarSystemName)
				{
								return $this->db->queryField("SELECT securityClass FROM mapSolarSystems WHERE solarSystemName = :name", "securityClass", array(":name" => $solarSystemName), 3600);
				}

}