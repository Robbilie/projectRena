<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class UsersGroups extends AbstractMigration
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
        $usersGroups = $this->table("usersGroups", array("engine" => "TokuDB"));
        $usersGroups
            ->addColumn("userID", "integer", array("limit" => 11))
            ->addColumn("groupID", "integer", array("limit" => 11))
            ->addColumn("groupType", "string", array("limit" => 255))
            ->addColumn("created", "datetime", array("default" => "CURRENT_TIMESTAMP"))
            ->addIndex(array("userID", "groupID"), array("unique" => true))
            ->addIndex(array("userID"))
            ->addIndex(array("groupID"))
            ->save();
    }
}
