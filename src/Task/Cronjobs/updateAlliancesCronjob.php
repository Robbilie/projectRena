<?php
namespace ProjectRena\Task\Cronjobs;

use ProjectRena\RenaApp;

/**
 * Updates and populates the alliance list, including corporations
 */
class updateAlliancesCronjob
{
    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public static function getRunTimes()
    {
        return 43200;
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

        \Resque::enqueue("now", "\\ProjectRena\\Task\\Resque\\updateAlliances");
        exit();
    }
}