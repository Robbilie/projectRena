<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class invFlags
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
				 * @param $flagID
				 *
				 * @return array
				 */
				public function getAllByID($flagID)
				{
								return $this->db->queryRow("SELECT * FROM invFlags WHERE flagID = :id", array(":id" => $flagID), 3600);
				}

				/**
				 * @param $flagName
				 *
				 * @return array
				 */
				public function getAllByName($flagName)
				{
								return $this->db->queryRow("SELECT * FROM invFlags WHERE flagName = :name", array(":name" => $flagName), 3600);
				}

				/**
				 * @param $flagID
				 *
				 * @return null
				 */
				public function getFlagNameByID($flagID)
				{
								return $this->db->queryField("SELECT flagName FROM invFlags WHERE flagID = :id", "", array(":id" => $flagID), 3600);
				}

				/**
				 * @param $flagName
				 *
				 * @return null
				 */
				public function getFlagIDByName($flagName)
				{
								return $this->db->queryField("SELECT flagID FROM invFlags WHERE flagName = :name", "", array(":name" => $flagName), 3600);
				}

				/**
				 * @param $flagID
				 *
				 * @return null
				 */
				public function getFlagTextByID($flagID)
				{
								return $this->db->queryField("SELECT flagText FROM invFlags WHERE flagID = :id", "", array(":id" => $flagID), 3600);
				}

				/**
				 * @param $flagName
				 *
				 * @return null
				 */
				public function getFlagTextByName($flagName)
				{
								return $this->db->queryField("SELECT flagText FROM invFlags WHERE flagName = :name", "", array(":name" => $flagName), 3600);
				}

				/**
				 * @param $flagID
				 *
				 * @return null
				 */
				public function getOrderIDByID($flagID)
				{
								return $this->db->queryField("SELECT orderID FROM invFlags WHERE flagID = :id", "", array(":id" => $flagID), 3600);
				}

				/**
				 * @param $flagName
				 *
				 * @return null
				 */
				public function getOrderIDByName($flagName)
				{
								return $this->db->queryField("SELECT orderID FROM invFlags WHERE flagName = :name", "", array(":name" => $flagName), 3600);
				}
}