<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class dgmEffects
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
				 * @param $effectID
				 *
				 * @return array
				 */
				public function getAllByID($effectID)
				{
								return $this->db->queryRow("SELECT * FROM dgmEffects WHERE effectID = :id", array(":id" => $effectID), 3600);
				}

				/**
				 * @param $effectName
				 *
				 * @return array
				 */
				public function getAllByName($effectName)
				{
								return $this->db->queryRow("SELECT * FROM dgmEffects WHERE effectName = :name", array(":name" => $effectName), 3600);
				}
}