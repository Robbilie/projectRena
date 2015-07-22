<?php

namespace ProjectRena\Task\Cronjobs;

use ProjectRena\RenaApp;

/**
 * Class updateCorporationsCronjob
 *
 * @package ProjectRena\Task\Cronjobs
 */
class updateCorporationsCronjob
{
    /**
     * @return int
     */
    public static function getRunTimes()
    {
        return 60; // Runs every 60 seconds
    }

    /**
     * @param $pid
     * @param $md5
     */
    public static function execute($pid, $md5)
    {
        $app = RenaApp::getInstance();
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