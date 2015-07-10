<?php
namespace ProjectRena\Task\Cronjobs;

use ProjectRena\RenaApp;

/**
 * Fetches Killmails and puts them into the killmails table
 */
class killMailFetcherCronjob
{

    /**
     * Executes the cronjob task
     *
     * @param mixed $pid
     * @param mixed $md5
     * @param RenaApp $app
     */
    public static function execute($pid, $md5, RenaApp $app)
    {
        if($app->Storage->get("Api904") >= date("Y-m-d H:i:s"))
            return;

        $toFetch = $app->Db->query("SELECT * FROM apiKeyCharacters WHERE cachedUntil < now() OR lastChecked < date_sub(now(), INTERVAL 24 HOUR) ORDER BY lastChecked LIMIT 500", array(), 1);

        if($toFetch)
            foreach($toFetch as $apiFetches)
                \Resque::enqueue("important", "\\ProjectRena\\Task\\Resque\\killMailFetcher", array("fetchData" => serialize($apiFetches)));

        exit(); // Keep this at the bottom, to make sure the fork exits
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public static function getRunTimes()
    {
        return 60; // Never runs
    }
}
