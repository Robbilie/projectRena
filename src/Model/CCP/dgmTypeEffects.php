<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class dgmTypeEffects
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
				function getAllByID($typeID)
				{
								return $this->db->queryRow("SELECT * FROM dgmTypeEffects WHERE typeID = :id", array(":id" => $typeID), 3600);
				}

				/**
				 * @param $typeID
				 *
				 * @return null
				 */
				function getEffectIDByID($typeID)
				{
								return $this->db->queryField("SELECT effectID FROM dgmTypeEffects WHERE typeID = :id", "effectID", array(":id" => $typeID), 3600);
				}

				/**
				 * @param $typeID
				 *
				 * @return null
				 */
				function getIsDefaultByID($typeID)
				{
								return $this->db->queryField("SELECT isDefault FROM dgmTypeEffects WHERE typeID = :id", "isDefault", array(":id" => $typeID), 3600);
				}
}