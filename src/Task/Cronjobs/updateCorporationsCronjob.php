<?php

namespace ProjectRena\Task\Cronjobs;

use ProjectRena\Lib\Db;
use ProjectRena\RenaApp;

class updateCorporationsCronjob
{
    public static function getRunTimes()
    {
        return 60; // Runs every 60 seconds
    }

    public static function execute($pid, $md5, RenaApp $app)
    {
        // Select 1000 characters which lastValidation was over 7 days ago, then update them
        $corporations = $app->Db->query("SELECT corporationID FROM corporations WHERE lastUpdated < date_sub(now(), interval 7 day) AND corporationID != 0 ORDER BY lastUpdated LIMIT 1000", array(), 0);
        if($corporations)
        {
            foreach($corporations as $corporation)
            {
                // Get the characterID to update
                $corporationID = $corporation["corporationID"];

                // Throw the ID after Resque which will update the corporations information
                \Resque::enqueue("default", "\\ProjectRena\\Task\\Resque\\updateCorporation", array("corporationID" => $corporationID));
            }
        }
        exit();
    }
}