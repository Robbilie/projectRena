<?php
namespace ProjectRena\Task;

use Cilex\Command\Command;
use ProjectRena\Lib;
use ProjectRena\RenaApp;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Updates the alliance list on demand instead of automatically
 */
class UpdateAllianceListTask extends Command
{

    /**
     */
    protected function configure()
    {
        $this->setName('update:alliancelist')->setDescription('Updates the alliance list on demand instead of automatically');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Init rena
        $app = RenaApp::getInstance();

        $app->StatsD->increment("ccpRequests");
        $data = $app->EVEEVEAllianceList->getData();
        if(isset($data["result"]["alliances"]))
        {
            foreach($data["result"]["alliances"] as $alliance)
            {
                $output->writeln("Updating/Adding: " . $alliance["name"]);

                // Update all the corporations in the alliance.. maybe we missed one?
                foreach($alliance["memberCorporations"] as $corporation)
                    \Resque::enqueue("default", "\\ProjectRena\\Task\\Resque\\updateCorporation", array("corporationID" => $corporation["corporationID"]));

                $allianceID = $alliance["allianceID"];
                $allianceName = $alliance["name"];
                $allianceTicker = $alliance["shortName"];
                $memberCount = $alliance["memberCount"];
                $executorCorporationID = $alliance["executorCorpID"];
                $information = json_decode($app->cURL->getData("https://public-crest.eveonline.com/alliances/{$allianceID}/"), true)["description"];
                $app->alliances->updateAllianceDetails($allianceID, $allianceName, $allianceTicker, $memberCount, $executorCorporationID, $information);
                $app->alliances->setLastUpdated($allianceID, date("Y-m-d H:i:s"));
            }
        }
    }
}
