<?php

use Phinx\Migration\AbstractMigration;

class UsersLogins extends AbstractMigration
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
        $usersLogins = $this->table("usersLogins", array("engine" => "TokuDB"));
        $usersLogins
            ->addColumn("userID", "integer", array("limit" => 11))
            ->addColumn("ipAddress", "string", array("limit" => 15))
            ->addColumn("ipHostname", "string", array("limit" => 255, "null" => true))
            ->addColumn("ipCountry", "string", array("limit" => 3, "null" => true))
            ->addColumn("created", "datetime", array("default" => "CURRENT_TIMESTAMP"))
            ->addIndex(array("userID", "ipAddress"), array("unique" => true))
            ->save();
    }
}
