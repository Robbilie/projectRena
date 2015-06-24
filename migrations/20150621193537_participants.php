<?php

use Phinx\Migration\AbstractMigration;

class Participants extends AbstractMigration
{
    public function down()
    {
        $this->execute("DROP INDEX killID ON participants");
        $this->execute("DROP INDEX killTime ON participants");
        $this->execute("DROP INDEX solarSystemID ON participants");
        $this->execute("DROP INDEX regionID ON participants");
        $this->execute("DROP INDEX shipTypeID ON participants");
        $this->execute("DROP INDEX groupID ON participants");
        $this->execute("DROP INDEX vGroupID ON participants");
        $this->execute("DROP INDEX weaponTypeID ON participants");
        $this->execute("DROP INDEX characterID ON participants");
        $this->execute("DROP INDEX corporationID ON participants");
        $this->execute("DROP INDEX allianceID ON participants");
        $this->execute("DROP INDEX factionID ON participants");
    }
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
        $participants = $this->table('participants', array("engine" => "TokuDB"));
        $participants
         ->addColumn("killID", "integer", array("limit" => 32))
         ->addColumn("killTime", "timestamp", array("default" => "0000-00-00 00:00:00"))
            // IDs
         ->addColumn("solarSystemID", "integer", array("limit" => 16))
         ->addColumn("regionID", "integer", array("limit" => 16))
         ->addColumn("characterID", "integer", array("limit" => 16))
         ->addColumn("corporationID", "integer", array("limit" => 16))
         ->addColumn("allianceID", "integer", array("limit" => 16))
         ->addColumn("factionID", "integer", array("limit" => 16))
         ->addColumn("shipTypeID", "integer", array("limit" => 8))
         ->addColumn("groupID", "integer", array("limit" => 8))
         ->addColumn("vGroupID", "integer", array("limit" => 8))
         ->addColumn("weaponTypeID", "integer", array("limit" => 8))
            // Extra values
         ->addColumn("shipValue", "decimal", array("limit" => 17))
         ->addColumn("damageDone", "integer", array("limit" => 8))
         ->addColumn("totalValue", "decimal", array("limit" => 17))
         ->addColumn("pointValue", "integer", array("limit" => 4))
         ->addColumn("numberInvolved", "integer", array("limit" => 4))
         ->addColumn("isVictim", "integer", array("limit" => 1))
         ->addColumn("finalBlow", "integer", array("limit" => 1))
         ->addColumn("isNPC", "integer", array("limit" => 1))
            // Indexes
         ->addIndex(array("isNPC", "killID"))
         ->addIndex(array("finalBlow", "killID"))
         ->addIndex(array("damageDone", "killID"))
         ->addIndex(array("shipValue", "killID"))
         ->addIndex(array("isVictim", "killID"))
         ->addIndex(array("numberInvolved", "killID"))
         ->addIndex(array("pointValue", "killID"))
         ->addIndex(array("totalValue", "killID"))
         ->save();

        // Clustered indexes in tokudb take up A LOT of space.. Old DB table was ~70GB (~70GB on disk), now it's +200GB, tho it only takes up ~60GB on disk
        $this->execute("SET tokudb_create_index_online=ON");
        if(!$participants->hasIndex("killID"))
            $this->execute("CREATE INDEX killID ON participants (killID) CLUSTERING=YES");
        if(!$participants->hasIndex("killTime"))
            $this->execute("CREATE INDEX killTime ON participants (killTime) CLUSTERING=YES");
        if(!$participants->hasIndex("solarSystemID"))
            $this->execute("CREATE INDEX solarSystemID ON participants (solarSystemID) CLUSTERING=YES");
        if(!$participants->hasIndex("regionID"))
            $this->execute("CREATE INDEX regionID ON participants (regionID) CLUSTERING=YES");
        if(!$participants->hasIndex("shipTypeID"))
            $this->execute("CREATE INDEX shipTypeID ON participants (shipTypeID) CLUSTERING=YES");
        if(!$participants->hasIndex("groupID"))
            $this->execute("CREATE INDEX groupID ON participants (groupID) CLUSTERING=YES");
        if(!$participants->hasIndex("vGroupID"))
            $this->execute("CREATE INDEX vGroupID ON participants (vGroupID) CLUSTERING=YES");
        if(!$participants->hasIndex("weaponTypeID"))
            $this->execute("CREATE INDEX weaponTypeID ON participants (weaponTypeID) CLUSTERING=YES");
        if(!$participants->hasIndex("characterID"))
            $this->execute("CREATE INDEX characterID ON participants (characterID) CLUSTERING=YES");
        if(!$participants->hasIndex("corporationID"))
            $this->execute("CREATE INDEX corporationID ON participants (corporationID) CLUSTERING=YES");
        if(!$participants->hasIndex("allianceID"))
            $this->execute("CREATE INDEX allianceID ON participants (allianceID) CLUSTERING=YES");
        if(!$participants->hasIndex("factionID"))
            $this->execute("CREATE INDEX factionID ON participants (factionID) CLUSTERING=YES");
        if(!$participants->hasIndex("killID")) // For some reason it can skip killID index creation, don't ask me why.........
            $this->execute("CREATE INDEX killID ON participants (killID) CLUSTERING=YES");
    }
}
