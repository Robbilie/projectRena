<?php

namespace ProjectRena\Task\Cronjobs;

use ProjectRena\RenaApp;

class updateCharactersCronjob
{
    public static function getRunTimes()
    {
        return 1; // Runs every 60 seconds
    }

    public static function execute($pid, $md5, $db, RenaApp $app)
    {
        // Select 1000 characters which lastValidation was over 7 days ago, then update them
        $characters = $db->query("SELECT * FROM characters WHERE lastUpdated < date_sub(now(), interval 7 day) ORDER BY lastUpdated LIMIT 1000", array(), 0);

        if($characters)
        {
            $cnt = 0;
            foreach($characters as $character)
            {
                $characterID = $character["characterID"];

                // Skip NPC and DUST characters
                if($characterID >= 2100000000 && $characterID <= 2200000000)
                    continue;
                if($characterID >= 30000000 && $characterID <= 31004590)
                    continue;
                if($characterID >= 40000000 && $characterID <= 41004590)
                    continue;

                // Get the character affiliate data
                $data = $app->EVEEVECharacterInfo->getData($characterID);

                // Update/Insert the character
                $app->characters->updateCharacterDetails($data["result"]["characterID"], $data["result"]["corporationID"], (isset($data["result"]["allianceID"]) ? $data["result"]["allianceID"] : 0), $data["result"]["characterName"], json_encode($data["result"]["employmentHistory"]));

                // Update the last updated
                $app->characters->setLastUpdated($characterID, date("Y-m-d H:i:s"));

                // Increment $cnt
                $cnt++;
            }
            echo "Processed {$cnt} characters";
        }
        exit();
    }
}