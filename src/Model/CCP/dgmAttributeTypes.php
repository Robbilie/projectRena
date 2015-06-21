<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class dgmAttributeTypes
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
				 * @param $attributeID
				 *
				 * @return array
				 */
				public function getAllByID($attributeID)
				{
								return $this->db->queryRow("SELECT * FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return array
				 */
				public function getAllByName($attributeName)
				{
								return $this->db->queryRow("SELECT * FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}

				/**
				 * @param $attributeID
				 *
				 * @return null
				 */
				public function getNameByID($attributeID)
				{
								return $this->db->queryField("SELECT attributeName FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return null
				 */
				public function getIDByName($attributeName)
				{
								return $this->db->queryField("SELECT attributeID FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}

				/**
				 * @param $attributeID
				 *
				 * @return null
				 */
				public function getDescriptionByID($attributeID)
				{
								return $this->db->queryField("SELECT description FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return null
				 */
				public function getDescriptionByName($attributeName)
				{
								return $this->db->queryField("SELECT description FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}

				/**
				 * @param $attributeID
				 *
				 * @return null
				 */
				public function getIconIDByID($attributeID)
				{
								return $this->db->queryField("SELECT iconID FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return null
				 */
				public function getIconIDByName($attributeName)
				{
								return $this->db->queryField("SELECT iconID FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}

				/**
				 * @param $attributeID
				 *
				 * @return null
				 */
				public function getDefaultValueByID($attributeID)
				{
								return $this->db->queryField("SELECT defaultValue FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return null
				 */
				public function getDefaultValueByName($attributeName)
				{
								return $this->db->queryField("SELECT defaultValue FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}

				/**
				 * @param $attributeID
				 *
				 * @return null
				 */
				public function getPublishedByID($attributeID)
				{
								return $this->db->queryField("SELECT published FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return null
				 */
				public function getPublishedByName($attributeName)
				{
								return $this->db->queryField("SELECT published FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}

				/**
				 * @param $attributeID
				 *
				 * @return null
				 */
				public function getDisplayNameByID($attributeID)
				{
								return $this->db->queryField("SELECT displayName FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return null
				 */
				public function getDisplayNameByName($attributeName)
				{
								return $this->db->queryField("SELECT displayName FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}

				/**
				 * @param $attributeID
				 *
				 * @return null
				 */
				public function getUnitIDByID($attributeID)
				{
								return $this->db->queryField("SELECT unitID FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return null
				 */
				public function getUnitIDByName($attributeName)
				{
								return $this->db->queryField("SELECT unitID FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}

				/**
				 * @param $attributeID
				 *
				 * @return null
				 */
				public function getStackableByID($attributeID)
				{
								return $this->db->queryField("SELECT stackable FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return null
				 */
				public function getStackableByName($attributeName)
				{
								return $this->db->queryField("SELECT stackable FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}

				/**
				 * @param $attributeID
				 *
				 * @return null
				 */
				public function getHighIsGoodByID($attributeID)
				{
								return $this->db->queryField("SELECT highIsGood FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return null
				 */
				public function getHighIsGoodByName($attributeName)
				{
								return $this->db->queryField("SELECT highIsGood FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}

				/**
				 * @param $attributeID
				 *
				 * @return null
				 */
				public function getCategoryIDByID($attributeID)
				{
								return $this->db->queryField("SELECT categoryID FROM dgmAttributeTypes WHERE attributeID = :id", "", array(":id" => $attributeID), 3600);
				}

				/**
				 * @param $attributeName
				 *
				 * @return null
				 */
				public function getCategoryIDByName($attributeName)
				{
								return $this->db->queryField("SELECT categoryID FROM dgmAttributeTypes WHERE attributeName = :name", "", array(":name" => $attributeName), 3600);
				}
}