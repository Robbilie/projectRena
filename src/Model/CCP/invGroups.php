<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class invGroups
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
				 * @param $groupID
				 *
				 * @return array
				 */
				public function getAllByID($groupID)
				{
								return $this->db->queryRow("SELECT * FROM invGroups WHERE groupID = :id", array(":id" => $groupID), 3600);
				}

				/**
				 * @param $groupName
				 *
				 * @return array
				 */
				public function getAllByName($groupName)
				{
								return $this->db->queryRow("SELECT * FROM invGroups WHERE groupName = :name", array(":name" => $groupName), 3600);
				}
}