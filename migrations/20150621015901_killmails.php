<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class Killmails extends AbstractMigration
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
        $users = $this->table('killmails', array("engine" => "TokuDB"));
        $users
         ->addColumn("killID", "integer", array("limit" => 32))
         ->addColumn("processed", "integer", array("limit" => 6))
         ->addColumn("hash", "string", array("limit" => 64))
         ->addColumn("source", "string", array("limit" => 64))
         ->addColumn("kill_json", "text", array("limit" => MysqlAdapter::TEXT_LONG))
         ->addColumn('dateAdded', 'datetime', array('default' => 'CURRENT_TIMESTAMP'))
         ->addIndex(array("killID"), array("unique" => true))
         ->addIndex(array("processed", "killID"))
         ->addIndex(array("hash", "killID"))
         ->addIndex(array("dateAdded", "killID"))
         ->save();
    }
}