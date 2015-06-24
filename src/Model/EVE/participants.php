<?php
namespace ProjectRena\Model\EVE;

use ProjectRena\RenaApp;

/**
 * Loads data from the participants table, and returns it how the query requests it
 */
class participants
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
     * @param array $extraArguments
     * @param null $offset
     * @param int $limit
     * @param $order
     *
     * @return array
     */
    private function verifyExtraArguments($extraArguments = array(), $offset = null, $limit = 100, $order = "DESC")
    {
        $queryString = "";
        $argumentArray = array();
        // Valid extra arguments
        $validArguments = array("killID", "killTime", "solarSystemID", "regionID", "characterID", "corporationID", "allianceID", "factionID", "shipTypeID", "groupID", "vGroupID", "weaponTypeID", "shipValue", "damageDone", "totalValue", "pointValue", "numberInvolved", "isVictim", "finalBlow", "isNPC");
        if(!empty($extraArguments))
        {
            foreach($validArguments as $argument)
            {
                if(isset($extraArguments[$argument]))
                {
                    $queryString .= " AND $argument = :$argument";
                    $argumentArray[":" . $argument] = $extraArguments[$argument];
                }
            }
        }

        if($offset > 0)
            $limit = "$offset, $limit ";

        $queryString .= " ORDER BY killTime $order LIMIT $limit";

        return array("queryString" => $queryString, "argumentArray" => $argumentArray);
    }

    /**
     * @param $killID
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByKillID($killID, $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments(array(), $offset, $limit, $order);
        $vQuery = $validated["queryString"];

        // Merge the arrays
        $array = array(":killID" => $killID);
        $query = "EXPLAIN SELECT * FROM participants WHERE killID = :killID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $killTime
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByKillTime($killTime, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":killTime" => $killTime), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE killTime = :killTime" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $solarSystemID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getBySolarSystemID($solarSystemID, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":solarSystemID" => $solarSystemID), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE solarSystemID = :solarSystemID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $regionID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByRegionID($regionID, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":regionID" => $regionID), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE regionID = :regionID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $characterID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByCharacterID($characterID, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":characterID" => $characterID), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE characterID = :characterID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $corporationID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByCorporationID($corporationID, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":corporationID" => $corporationID), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE corporationID = :corporationID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $allianceID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByAllianceID($allianceID, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":allianceID" => $allianceID), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE allianceID = :allianceID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $factionID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByFactionID($factionID, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":factionID" => $factionID), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE factionID = :factionID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $shipTypeID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByShipTypeID($shipTypeID, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":shipTypeID" => $shipTypeID), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE shipTypeID = :shipTypeID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $groupID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByGroupID($groupID, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":groupID" => $groupID), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE groupID = :groupID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $vGroupID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByVGroupID($vGroupID, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":vGroupID" => $vGroupID), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE vGroupID = :vGroupID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }

    /**
     * @param $weaponTypeID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getByWeaponTypeID($weaponTypeID, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null)
    {
        // Validate extraArguments
        $validated = $this->verifyExtraArguments($extraArguments, $offset, $limit, $order);
        $vQuery = $validated["queryString"];
        $vArray = $validated["argumentArray"];

        // Merge the arrays
        $array = array_merge(array(":weaponTypeID" => $weaponTypeID), $vArray);
        $query = "EXPLAIN SELECT * FROM participants WHERE weaponTypeID = :weaponTypeID" . $vQuery;

        // Execute the query
        return $this->db->query($query, $array, $cacheTime);
    }
}
