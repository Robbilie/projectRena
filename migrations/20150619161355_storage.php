<?php

use Phinx\Migration\AbstractMigration;

class Storage extends AbstractMigration
{
				/**
				 * Change Method.
				 *
				 * Write your reversible migrations using this method.
				 *
				 * More information on writing migrations is available here:
				 * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
				 */
				public function change()
				{
								$users = $this->table('storage', array("engine" => "TokuDB"));
								$users
									->addColumn('key', 'string', array('limit' => 255))
									->addColumn('value', 'string', array('limit' => 255))
									->addIndex(array('key'), array('unique' => true))
									->save();
				}
}
