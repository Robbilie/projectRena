<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class Corporations extends AbstractMigration
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
        $corporations = $this->table('corporations', array("engine" => "TokuDB"));
        $corporations
         ->addColumn("corporationID", "integer", array("limit" => 16))
         ->addColumn("allianceID", "integer", array("limit" => 16))
         ->addColumn("corporationName", "string", array("limit" => 128))
         ->addColumn("ceoID", "integer", array("limit" => 16))
         ->addColumn("corpTicker", "string", array("limit" => 6))
         ->addColumn("memberCount", "integer", array("limit" => 5))
         ->addColumn('information', 'text', array("limit" => MysqlAdapter::TEXT_MEDIUM))
         ->addColumn('dateAdded', 'datetime', array('default' => 'CURRENT_TIMESTAMP'))
         ->addColumn('lastUpdated', 'datetime', array('default' => '0000-00-00 00:00:00'))
         ->addIndex(array("corporationID"))
         ->addIndex(array("allianceID"))
         ->addIndex(array("corporationName"))
         ->addIndex(array("lastUpdated"))
         ->save();
    }
}
