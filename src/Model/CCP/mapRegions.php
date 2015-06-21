<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class mapRegions
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
				 * @param $regionID
				 *
				 * @return array
				 */
				public function getAllByID($regionID)
				{
								return $this->db->queryRow("SELECT * FROM mapRegions WHERE regionID = :id", array(":id" => $regionID), 3600);
				}

				/**
				 * @param $regionName
				 *
				 * @return array
				 */
				public function getAllByName($regionName)
				{
								return $this->db->queryRow("SELECT * FROM mapRegions WHERE regionName = :name", array(":name" => $regionName), 3600);
				}

				/**
				 * @param $regionID
				 *
				 * @return null
				 */
				public function getRegionNameByID($regionID)
				{
								return $this->db->queryField("SELECT regionName FROM mapRegions WHERE regionID = :id", "", array(":id" => $regionID), 3600);
				}

				/**
				 * @param $regionName
				 *
				 * @return null
				 */
				public function getRegionIDByName($regionName)
				{
								return $this->db->queryField("SELECT regionID FROM mapRegions WHERE regionName = :name", "", array(":name" => $regionName), 3600);
				}

				/**
				 * @param $regionID
				 *
				 * @return null
				 */
				public function getCoordinatesByID($regionID)
				{
								return $this->db->queryField("SELECT x, y, z, xMin, xMax, yMin, yMax, zMin, zMax, radius FROM mapRegions WHERE regionID = :id", "", array(":id" => $regionID), 3600);
				}

				/**
				 * @param $regionName
				 *
				 * @return null
				 */
				public function getCoordinatesByName($regionName)
				{
								return $this->db->queryField("SELECT x, y, z, xMin, xMax, yMin, yMax, zMin, zMax, radius FROM mapRegions WHERE regionName = :name", "", array(":name" => $regionName), 3600);
				}

				/**
				 * @param $regionID
				 *
				 * @return null
				 */
				public function getFactionIDByID($regionID)
				{
								return $this->db->queryField("SELECT factionID FROM mapRegions WHERE regionID = :id", "", array(":id" => $regionID), 3600);
				}

				/**
				 * @param $regionName
				 *
				 * @return null
				 */
				public function getFactionIDByName($regionName)
				{
								return $this->db->queryField("SELECT factionID FROM mapRegions WHERE regionName = :name", "", array(":name" => $regionName), 3600);
				}

}