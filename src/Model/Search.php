<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

/**
 * Searches for items, characters, corps etc. in the entire database and returns an array with what it has found
 */
class Search
{

    /**
     * The Slim Application
     */
    private $app;

    /**
     * The Cache
     */
    private $cache;

    /**
     * The baseConfig (config/config.php)
     */
    private $config;

    /**
     * cURL interface (getData / setData)
     */
    private $curl;

    /**
     * The Database
     */
    private $db;

    /**
     * The logger, outputs to logs/app.log
     */
    private $log;

    /**
     * StatsD for tracking stats
     */
    private $statsd;

    /**
     * @param RenaApp $app
     */
    public function __construct(RenaApp $app)
    {
        $this->app = $app;
        $this->db = $app->Db;
        $this->config = $app->baseConfig;
        $this->cache = $app->Cache;
        $this->curl = $app->cURL;
        $this->statsd = $app->StatsD;
        $this->log = $app->Logging;
    }

    /**
     * @param $searchTerm
     * @param array $searchIn array("faction", "alliance", "corporation", "character", "item", "system", "region")
     *
     * @return array
     */
    public function search($searchTerm, $searchIn = array("faction", "alliance", "corporation", "character", "item", "system", "region"))
    {
        $valid = array("faction", "alliance", "corporation", "character", "item", "system", "region");
        $searchArray = array();
        foreach($searchIn as $lookIn)
            if(in_array($lookIn, $valid))
                if(count($this->$lookIn($searchTerm)) > 0)
                    $searchArray[$lookIn] = $this->$lookIn("%" . $searchTerm . "%");

        return $searchArray;
    }

    private function faction($searchTerm)
    {
        return $this->db->query("SELECT factionID, name FROM factions WHERE (name LIKE :searchTerm OR ticker LIKE :searchTerm) LIMIT 5", array(":searchTerm" => $searchTerm));
    }

    private function alliance($searchTerm)
    {
        return $this->db->query("SELECT allianceID, allianceName, allianceTicker FROM alliances WHERE (allianceName LIKE :searchTerm OR allianceTicker LIKE :searchTerm) LIMIT 5", array(":searchTerm" => $searchTerm));
    }

    private function corporation($searchTerm)
    {
        return $this->db->query("SELECT corporationID, corporationName, corpTicker FROM corporations WHERE (corporationName LIKE :searchTerm OR corpTicker LIKE :searchTerm) LIMIT 5", array(":searchTerm" => $searchTerm));
    }

    private function character($searchTerm)
    {
        return $this->db->query("SELECT characterID, characterName FROM characters WHERE characterName LIKE :searchTerm LIMIT 5", array(":searchTerm" => $searchTerm));
    }

    private function item($searchTerm)
    {
        return $this->db->query("SELECT typeID, typeName FROM invTypes WHERE typeName LIKE :searchTerm LIMIT 5", array(":searchTerm" => $searchTerm));
    }

    private function system($searchTerm)
    {
        return $this->db->query("SELECT solarSystemID, solarSystemName FROM mapSolarSystems WHERE solarSystemName LIKE :searchTerm LIMIT 5", array(":searchTerm" => $searchTerm));
    }

    private function region($searchTerm)
    {
        return $this->db->query("SELECT regionID, regionName FROM mapRegions WHERE regionName LIKE :searchTerm LIMIT 5", array(":searchTerm" => $searchTerm));
    }

}
