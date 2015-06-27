<?php
namespace ProjectRena\Task\Cronjobs;

use ProjectRena\Lib\Db;
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
     * Executes the cronjob task
     *
     * @param mixed $pid
     * @param mixed $md5
     * @param RenaApp $app
     */
    public static function execute($pid, $md5, RenaApp $app)
    {
        \Resque::enqueue("now", "\\ProjectRena\\Task\\Resque\\updateAlliances");
        exit();
    }
}