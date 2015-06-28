<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

/**
 * Model for getting and setting data in the APIKeyCharacters table
 */
class ApiKeyCharacters
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
     * @param $keyID
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getAllByID($keyID)
    {
        return $this->db->query("SELECT * FROM apiKeyCharacters WHERE keyID = :keyID", array(":keyID" => $keyID));
    }

    /**
     * @param $characterID
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getAllByCharacterID($characterID)
    {
        return $this->db->query("SELECT * FROM apiKeyCharacters WHERE characterID = :characterID", array(":characterID" => $characterID));
    }

    /**
     * @param $corporationID
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getAllByCorporationID($corporationID)
    {
        return $this->db->query("SELECT * FROM apiKeyCharacters WHERE corporationID = :corporationID", array(":corporationID" => $corporationID));
    }

    /**
     * @param $keyID
     * @param $characterID
     * @param $errorCode
     *
     * @return array|bool
     * @throws \Exception
     */
    public function setErrorCode($keyID, $characterID, $errorCode)
    {
        return $this->db->execute("UPDATE apiKeyCharacters SET errorCode = :errorCode WHERE keyID = :keyID AND characterID = :characterID", array(":errorCode" => $errorCode, ":keyID" => $keyID, ":characterID" => $characterID));
    }

    /**
     * @param $keyID
     * @param $characterID
     * @param $lastChecked
     *
     * @return array|bool
     * @throws \Exception
     */
    public function setLastChecked($keyID, $characterID, $lastChecked)
    {
        return $this->db->execute("UPDATE apiKeyCharacters SET lastChecked = :lastChecked WHERE keyID = :keyID AND characterID = :characterID", array(":lastChecked" => $lastChecked, ":keyID" => $keyID, ":characterID" => $characterID));
    }

    /**
     * @param $keyID
     * @param $characterID
     * @param $cachedUntil
     *
     * @return array|bool
     * @throws \Exception
     */
    public function setCachedUntil($keyID, $characterID, $cachedUntil)
    {
        return $this->db->execute("UPDATE apiKeyCharacters SET cachedUntil = :cachedUntil WHERE keyID = :keyID AND characterID = :characterID", array(":cachedUntil" => $cachedUntil, ":keyID" => $keyID, ":characterID" => $characterID));
    }

    /**
     * @param $keyID
     * @param $characterID
     * @param $maxKillID
     *
     * @return array|bool
     * @throws \Exception
     */
    public function setMaxKillID($keyID, $characterID, $maxKillID)
    {
        return $this->db->execute("UPDATE apiKeyCharacters SET maxKillID = :maxKillID WHERE keyID = :keyID AND characterID = :characterID", array(":maxKillID" => $maxKillID, ":keyID" => $keyID, ":characterID" => $characterID));
    }

    /**
     * @param $keyID
     * @param $characterID
     * @param $isDirector
     *
     * @return array|bool
     * @throws \Exception
     */
    public function setIsDirector($keyID, $characterID, $isDirector)
    {
        return $this->db->execute("UPDATE apiKeyCharacters SET isDirector = :isDirector WHERE keyID = :keyID AND characterID = :characterID", array(":isDirector" => $isDirector, ":keyID" => $keyID, ":characterID" => $characterID));
    }
}
