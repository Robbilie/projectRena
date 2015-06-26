<?php
use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class Paste extends AbstractMigration
{
				/**
				 * Change Method.
				 *
				 * More information on this method is available here:
				 * http://docs.phinx.org/en/latest/migrations.html#the-change-method
				 *
				 * Uncomment this method if you would like to use it.
				 *
				 * public function change()
				 * {
				 * }
				 */

				/**
				 * Migrate Up.
				 */
				public function up()
				{
								$paste = $this->table('paste', array("engine" => "TokuDB"));
								$paste
									->addColumn('hash', 'string', array('limit' => 64))
									->addColumn('userID', 'integer', array('limit' => 5))
									->addColumn('data', 'text', array("limit" => MysqlAdapter::TEXT_LONG))
									->addColumn('timeout', 'datetime', array("default" => '0000-00-00 00:00:00'))
									->addColumn('created', 'datetime', array("default" => 'CURRENT_TIMESTAMP'))
									->addIndex(array('hash'), array('unique' => true))
									->save();
				}

				/**
				 * Migrate Down.
				 */
				public function down()
				{

				}
}