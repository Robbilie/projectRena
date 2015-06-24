<?php

use Phinx\Migration\AbstractMigration;

class users extends AbstractMigration
{
				/**
				 * Change Method.
				 *
				 * More information on this method is available here:
				 * http://docs.phinx.org/en/latest/migrations.html#the-change-method
				 *
				 * Uncomment this method if you would like to use it.
				 */

				/**
				 * Migrate Up.
				 */
				public function up()
				{
								$users = $this->table("userstest", array("engine" => "TokuDB"));
								$users
									->addColumn("characterName", "string", array("limit" => 128))
									->addColumn("characterID", "integer", array("limit" => 11))
									->addColumn("characterOwnerHash", "string", array("limit" => 64))
									->addColumn("loginHash", "string", array("limit" => 64))
									->addColumn("accessToken", "string", array("limit" => 255))
									->addColumn("refreshToken", "string", array("limit" => 255, "null" => true))
									->addColumn("scopes", "string", array("limit" => 255, "null" => true))
									->addColumn("tokenType", "string", array("limit" => 255))
									->addColumn("created", "datetime", array("default" => "CURRENT_TIMESTAMP"))
									->addColumn("updated", "datetime", array("null" => true))
									->addIndex(array("characterName", "characterOwnerHash"), array("unique" => true))
									->save();
				}

				/**
				 * Migrate Down.
				 */
				public function down()
				{
				}
}