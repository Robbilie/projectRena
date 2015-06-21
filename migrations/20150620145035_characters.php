<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class Characters extends AbstractMigration
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
        $users = $this->table('characters', array("engine" => "TokuDB"));
        $users
         ->addColumn("characterID", "integer", array("limit" => 16))
         ->addColumn("corporationID", "integer", array("limit" => 16))
         ->addColumn("allianceID", "integer", array("limit" => 16))
         ->addColumn("characterName", "string", array("limit" => 128))
         ->addColumn('history', 'text', array("limit" => MysqlAdapter::TEXT_MEDIUM))
         ->addColumn('dateAdded', 'datetime', array('default' => 'CURRENT_TIMESTAMP'))
         ->addColumn('lastUpdated', 'datetime', array('default' => '0000-00-00 00:00:00'))
         ->addIndex(array("characterID", "characterName"), array("unique" => true))
         ->addIndex(array("corporationID"))
         ->addIndex(array("allianceID"))
         ->addIndex(array("characterName"))
         ->save();
    }
}
