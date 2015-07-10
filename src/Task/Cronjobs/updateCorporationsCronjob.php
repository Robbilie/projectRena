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
        if($app->Storage->get("Api904") >= date("Y-m-d H:i:s"))
            return;

        $corporations = $app->Db->query("SELECT corporationID FROM corporations WHERE lastUpdated < date_sub(now(), interval 7 day) AND corporationID != 0 ORDER BY lastUpdated LIMIT 500", array(), 0);
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