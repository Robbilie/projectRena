<?php

namespace ProjectRena\Task\Cronjobs;

use ProjectRena\Lib\Db;
use ProjectRena\RenaApp;

class updateCharactersCronjob
{
    public static function getRunTimes()
    {
        return 60; // Runs every 60 seconds
    }

    public static function execute($pid, $md5, RenaApp $app)
    {
        if($app->Storage->get("Api904") >= date("Y-m-d H:i:s"))
            return;

        $characters = $app->Db->query("SELECT characterID FROM characters WHERE lastUpdated < date_sub(now(), interval 7 day) AND characterID != 0 ORDER BY lastUpdated LIMIT 500", array(), 0);
        if($characters)
        {
            foreach($characters as $character)
            {
                // Get the characterID to update
                $characterID = $character["characterID"];

                // Throw the ID after Resque which will update the characters information
                \Resque::enqueue("default", "\\ProjectRena\\Task\\Resque\\updateCharacter", array("characterID" => $characterID));
            }
        }
        exit();
    }
}