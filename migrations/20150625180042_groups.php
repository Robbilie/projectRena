<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class Groups extends AbstractMigration
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
        $groups = $this->table("groups", array("engine" => "TokuDB"));
        $groups
            ->addColumn("groupID", "integer", array("limit" => 11))
            ->addColumn("groupName", "string", array("limit" => 255))
            ->addColumn("ownerID", "integer", array("limit" => 11))
            ->addColumn("admins", "text", array("limit" => MysqlAdapter::TEXT_MEDIUM)) // Json array with userIDs that can admin the group (Change settings etc)
            ->addColumn("moderators", "text", array("limit" => MysqlAdapter::TEXT_MEDIUM)) // Json array with userIDs that can moderate the group (Add/Kick users)
            ->addColumn("created", "datetime", array("default" => "CURRENT_TIMESTAMP"))
            ->addIndex(array("groupName"), array("unique" => true))
            ->addIndex(array("groupID"), array("unique" => true))
            ->addIndex(array("ownerID"))
            ->save();

    }
}