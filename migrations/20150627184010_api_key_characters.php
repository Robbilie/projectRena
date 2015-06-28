<?php

use Phinx\Migration\AbstractMigration;

class ApiKeyCharacters extends AbstractMigration
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
        $apiKeyCharacters = $this->table('apiKeyCharacters', array("engine" => "TokuDB"));
        $apiKeyCharacters
            ->addColumn("keyID", "integer", array("limit" => 16))
            ->addColumn("characterID", "integer", array("limit" => 16))
            ->addColumn("corporationID", "integer", array("limit" => 32))
            ->addColumn("isDirector", "integer", array("limit" => 1))
            ->addColumn("maxKillID", "integer", array("limit" => 32))
            ->addColumn("cachedUntil", "datetime", array("default" => "0000-00-00 00:00:00"))
            ->addColumn("lastChecked", "datetime", array("default" => "0000-00-00 00:00:00"))
            ->addColumn("errorCode", "integer", array("limit" => 6))
            ->addColumn("errorCount", "integer", array("limit" => 3))
            ->addIndex(array("keyID", "characterID"), array("unique" => true))
            ->addIndex(array("characterID"))
            ->addIndex(array("corporationID"))
            ->save();
    }
}
