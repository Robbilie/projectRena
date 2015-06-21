<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class dgmAttributeCategories
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
				 * @param $categoryID
				 *
				 * @return array
				 */
				public function getAllByID($categoryID)
				{
								return $this->db->queryRow("SELECT * FROM dgmAttributeCategories WHERE categoryID = :id", array(":id" => $categoryID), 3600);
				}

				/**
				 * @param $categoryName
				 *
				 * @return array
				 */
				public function getAllByName($categoryName)
				{
								return $this->db->queryRow("SELECT * FROM dgmAttributeCategories WHERE categoryName = :name", array(":name" => $categoryName), 3600);
				}

				/**
				 * @param $categoryID
				 *
				 * @return null
				 */
				public function getCategoryDescriptionByID($categoryID)
				{
								return $this->db->queryField("SELECT categoryDescription FROM dgmAttributeCategories WHERE categoryID = :id", "categoryDescription", arraY(":id" => $categoryID), 3600);
				}

				/**
				 * @param $categoryName
				 *
				 * @return null
				 */
				public function getCategoryDescriptionByName($categoryName)
				{
								return $this->db->queryField("SELECT categoryDescription FROM dgmAttributeCategories WHERE categoryName = :name", "categoryDescription", arraY(":name" => $categoryName), 3600);
				}

}