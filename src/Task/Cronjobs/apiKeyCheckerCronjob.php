<?php
namespace ProjectRena\Task\Cronjobs;

use ProjectRena\RenaApp;

/**
 * Takes API Keys and checks them for accessmask, and a few other things
 */
class apiKeyCheckerCronjob
{

    /**
     * @param $pid
     * @param $md5
     */
    public static function execute($pid, $md5)
    {
        $app = RenaApp::getInstance();
        if($app->Storage->get("Api904") >= date("Y-m-d H:i:s"))
            return;

        $apiKeys = $app->Db->query("SELECT keyID, vCode FROM apiKeys WHERE lastValidation < date_sub(now(), INTERVAL 6 HOUR) ORDER BY lastValidation DESC LIMIT 500", array(), 0);
        if($apiKeys)
        {
            foreach($apiKeys as $api)
            {
                $keyID = $api["keyID"];
                $vCode = $api["vCode"];
                // Update the lastValidation to in ten minutes, just so we don't insert it again and again
                \Resque::enqueue("default", "\\ProjectRena\\Task\\Resque\\updateApiKeys", array("keyID" => $keyID, "vCode" => $vCode));
            }
        }
        exit();
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public static function getRunTimes()
    {
        return 60; // Never runs
    }
}
