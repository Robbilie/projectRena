<?php
namespace ProjectRena\Task\Resque;

use ProjectRena\RenaApp;
use ZMQ;
use ZMQContext;

/**
 * Fetches killmails and puts them into the killmails table
 */
class killMailFetcher
{
    /**
     * @var
     */
    protected $app;

    /**
     * Performs the task, can access all $this->crap setup in setUp)
     */
    public function perform()
    {
        if($this->app->Storage->get("Api904") >= date("Y-m-d H:i:s"))
            return;

        $this->app->StatsD->increment("ccpRequests");
        $fetchData = unserialize($this->args["fetchData"]);
        $keyID = $fetchData["keyID"];
        $vCode = $this->app->ApiKeys->getVCodeByKeyID($keyID);
        $characterID = $fetchData["characterID"];
        $maxKillID = $fetchData["maxKillID"];
        $isDirector = $fetchData["isDirector"];

        if($isDirector)
            $data = $this->getData($keyID, $vCode);
        else
            $data = $this->getData($keyID, $vCode, $characterID);

        $cachedUntil = $data["cachedUntil"];
        $maxKillID = 0;
        $kills = $data["result"]["kills"];

        if(!empty($kills))
        {
            foreach($kills as $kill)
            {
                // Remove that bloody string value thing
                unset($kill["_stringValue"]);

                // Set the killID
                $killID = (int) $kill["killID"];
                $kill["killID"] = (int) $kill["killID"];

                // Generate the hash
                $hash = hash("sha256", ":" . $kill["killTime"] . ":" . $kill["solarSystemID"] . ":" . $kill["moonID"] . "::" . $kill["victim"]["characterID"] . ":" . $kill["victim"]["shipTypeID"] . ":" . $kill["victim"]["damageTaken"] . ":");

                // Create the source
                $source = "apiKey:" . $keyID;

                // json encode the killData
                $json = json_encode($kill);

                // insert the killData to the killmails table
                $inserted = $this->app->killmails->insertKillmail($killID, 0, $hash, $source, $json);

                // Update the maxKillID
                $maxKillID = max($maxKillID, $killID);

                // Push it to the queue if it inserted, also poke statsd to increment the tracker for it
                if($inserted > 0)
                {
                    $this->app->StatsD->increment("killmailsAdded");

                    // Push it over zmq to the websocket
                    $context = new ZMQContext();
                    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, "rena");
                    $socket->connect("tcp://localhost:5555");
                    $socket->send($json);
                }
            }
        }

        $this->app->ApiKeyCharacters->setMaxKillID($keyID, $characterID, $maxKillID);
        $this->app->ApiKeyCharacters->setCachedUntil($keyID, $characterID, $cachedUntil);
        $this->app->ApiKeyCharacters->setLastChecked($keyID, $characterID, date("Y-m-d H:i:s"));
    }

    /**
     * Sets up the task (Setup $this->crap and such here)
     */
    public function setUp()
    {
        $this->app = RenaApp::getInstance();
    }

    /**
     * Tears the task down, unset $this->crap and such
     */
    public function tearDown()
    {
        $this->app = null;
    }

    /**
     * @param $apiKey
     * @param $vCode
     * @param null $characterID
     * @param null $fromID
     * @param null $rowCount
     *
     * @return mixed
     */
    private function getData($apiKey, $vCode, $characterID = null, $fromID = null, $rowCount = null)
    {
        if(!$characterID)
            $data = $this->app->EVECorporationKillMails->getData($apiKey, $vCode, $fromID, $rowCount);
        else
            $data = $this->app->EVECharacterKillMails->getData($apiKey, $vCode, $characterID, $fromID, $rowCount);

        return $data;
    }
}
