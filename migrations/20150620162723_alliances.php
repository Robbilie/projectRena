<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class Alliances extends AbstractMigration
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
        $users = $this->table('alliances');
        $users
         ->addColumn("allianceID", "integer", array("limit" => 16))
         ->addColumn("allianceName", "string", array("limit" => 128))
         ->addColumn("allianceTicker", "string", array("limit" => 8))
         ->addColumn("memberCount", "integer", array("limit" => 10))
         ->addColumn("executorCorporationID", "integer", array("limit" => 16))
         ->addColumn('information', 'text', array("limit" => MysqlAdapter::TEXT_MEDIUM))
         ->addColumn('dateAdded', 'datetime', array('default' => 'CURRENT_TIMESTAMP'))
         ->addColumn('lastUpdated', 'datetime', array('default' => '0000-00-00 00:00:00'))
         ->addIndex(array("allianceID", "allianceName"), array("unique" => true))
         ->addIndex(array("allianceID"))
         ->save();
    }
}