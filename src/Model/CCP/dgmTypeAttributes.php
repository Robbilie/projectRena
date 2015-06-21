<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class dgmTypeAttributes
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
				 * @param $typeID
				 *
				 * @return array
				 */
				public function getAllByID($typeID)
				{
								return $this->db->queryRow("SELECT * FROM dgmTypeAttributes WHERE typeID = :id", array(":typeID" => $typeID), 3600);
				}

				/**
				 * @param $typeID
				 *
				 * @return null
				 */
				public function getAttributeIDByID($typeID)
				{
								return $this->db->queryField("SELECT attributeID FROM dgmTypeAttributes WHERE typeID = :id", "attributeID", array(":id" => $typeID), 3600);
				}

				/**
				 * @param $typeID
				 *
				 * @return null
				 */
				public function getValueIntByID($typeID)
				{
								return $this->db->queryField("SELECT valueInt FROM dgmTypeAttributes WHERE typeID = :id", "valueInt", array(":id" => $typeID), 3600);
				}

				/**
				 * @param $typeID
				 *
				 * @return null
				 */
				public function getValueFloatByID($typeID)
				{
								return $this->db->queryField("SELECT valueFloat FROM dgmTypeAttributes WHERE typeID = :id", "valueFloat", array(":id" => $typeID), 3600);
				}
}